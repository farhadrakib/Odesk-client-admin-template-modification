<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php

class BalanceCalculator
{
	/**
	 * Means package templates available for creating plans from. Assoc with packid as a key
	 * $pack['title'];//--- optional here ---
	 * $pack['domains'];//domains included in pack
	 * $pack['extradomain_weekly'];//extra domain price
	 * $pack['extradomain_biweekly'];//extra domain price
	 * $pack['extradomain_monthly'];//extra domain price
	 * $pack['weekly'];//weekly billing for pack with 0 extra domains
	 * $pack['biweekly'];//biweekly billing for pack with 0 extra domains
	 * $pack['monthly'];//monthly billing for pack with 0 extra domains
	 * $pack['annually'];//annually billing for pack with 0 extra domains
	 */
	protected $packs = array();


	/**
	 * All user transactions
	 * $tran['id'];//tran pk --- optional here ---
	 * $tran['txnid'];//paypal tran id --- optional here ---
	 * $tran['user'];//user paypal email --- optional here ---
	 * $tran['payment_status'];// 'completed', 'pending' == '' --- optional here ---
	 * $tran['payment_amount'];//tran amount in USD always
	 * $tran['createdtime'];// "Y-m-d H:i:s" not timestamp,
	 * $tran['txn_type'];// refund, suspend, resume, payment == ''
	 */
	protected $trans = array();

	/**
	 * All plans applied to user. Assoc array with plan_id as a key.
	 * $plan['pack_id'];//package id
	 * $plan['pack_domains'];//fixed domains count included in pack
	 * $plan['cost_weekly'];//fixed price for weekly usage
	 * $plan['cost_biweekly'];//fixed price for biweekly usage
	 * $plan['cost_monthly'];//fixed monthly price
	 * $plan['cost_annually'];//fixed annually price
	 * $plan['extradomain_weekly'];//current fixed price for extra domain
	 * $plan['extradomain_biweekly'];//current fixed price for extra domain
	 * $plan['extradomain_monthly'];//current fixed price for extra domain
	 * $plan['extradomains_count'];//additional extra domains selected
	 * $plan['pay_interval'];//'weekly','biweekly', 'monthly', 'annual' - current pay interval selected for this plan
	 * $plan['fixed'];//1 or 0 - shows if plan is fixed at creation time or dynamicly determined by cur pack settings
	 * $plan['start_date'];//timestamp, not  "Y-m-d H:i:s"
	 * $plan['end_date'];//timestamp, not  "Y-m-d H:i:s"
	 * $plan['suspensions'];//array('start' => time(), 'end' => time() or false)
	 */
	protected $plans = array();

	protected $now = 0;

	public function __construct($timestamp_now = null)
	{
		$this->now = $timestamp_now;
		if (empty($this->now))
			$this->now = time();
	}

	public function Balance($now = null)
	{
		$now = empty($now) ? $this->now : $now;

		$pack_usage      = $this->calcPackUsage($now);
		$payments_amount = $this->calcPaymentsBalance($now);

		//print_r(compact('pack_usage', 'payments_amount'));

		return $payments_amount - $pack_usage;
	}

	public function DetectStatus($status, $now = null)
	{
		$now = empty($now) ? $this->now : $now;

		// Overriding status set by administrator
		if($status == 6 || $status == 8 || $status == 9 ) {
			return $status;
		}

		$active_plan = $this->GetActivePlan();
		if(is_plan_free($active_plan)) {
			return 7;
		}

		$payments = $this->SearchTrans(array('payment', 'manual'));
		if (count($payments) == 0)
			return 0;
		//created

		$balance = $this->Balance();

		if ($balance > 0)
			return 1;
		//active

		//detect if latest transaction is refund or payment
		$refunds = $this->SearchTrans('refund', $active_plan);
		if (count($refunds) > 0) {
			$payments    = $this->SearchTrans('payment', $active_plan);
			$lastRefund  = 0;
			$lastPayment = 0;
			//find latest refund
			foreach ($refunds as $refund) {
				$time       = strtotime($refund['createdtime']);
				$lastRefund = $lastRefund < $time ? $time : $lastRefund;
			}
			//find latest payment
			foreach ($payments as $payment) {
				$time        = strtotime($payment['createdtime']);
				$lastPayment = $lastPayment < $time ? $time : $lastPayment;
			}

			if ($lastRefund > $lastPayment)
				return 4;
		}

		$daily_cost = $this->DailyCost($active_plan);
		if ($daily_cost + $balance >= 0) //he dues less then for a day
			return 2;

		//pending

		return 3; //suspended
	}


	public function GetActivePlan($now = null)
	{
		$now     = empty($now) ? $this->now : $now;
		$lastInd = count($this->plans) - 1;
		for ($i = $lastInd; $i >= 0; $i--) { //it should be last as well
			$plan = & $this->plans[$i];
			if ($plan['start_date'] <= $now && empty($plan['end_date'])) {
				return $this->plans[$i];
			}
		}

		return null;
	}

	public function SearchTrans($txn_type = null, $plan = null, $now = null)
	{
		$now   = empty($now) ? $this->now : $now;
		$res   = array();
		$start = 0;
		if ($plan != null) {
			$start = $plan['start_date'];
			$now   = empty($plan['end_date']) ? $now : $plan['end_date'];
		}

		$is_txn_type_array = is_array($txn_type);
		foreach ($this->trans as $txn) {
			$time = @strtotime($txn['createdtime']);
			if ($time >= $start && $time <= $now &&
				(empty($txn_type) ||
					$is_txn_type_array && in_array($txn['txn_type'], $txn_type) ||
					!$is_txn_type_array && $txn_type == $txn['txn_type'])
			)
				$res[] = $txn;
		}

		return $res;
	}

	/**
	 * Calculates one time payment for the plan using its settings
	 */
	public function CalcPlanPayment(&$plan)
	{
		if (!is_array($plan) || empty($plan))
			return 0;

		$sum = (float)$plan['cost_' . $plan['pay_interval']];
		//if ($plan['extradomains_enabled'])
		$sum += (float)$plan['extradomain_' . $plan['pay_interval']] * (int)$plan['extradomains_count'];

		return $sum;
	}


	public function DailyCost(&$plan = null)
	{
		$plan             = empty($plan) ? $this->GetActivePlan() : $plan;
		$plan_cost        = $this->CalcPlanPayment($plan);
		$pay_interval_sec = $this->PayIntervalInSeconds($plan);

		$daily_cost = 0;
		if($pay_interval_sec > 0) {
			$daily_cost = $plan_cost * 3600 * 24 * 1.0 / $pay_interval_sec;
		}

//		print_r(compact('plan', 'plan_cost', 'pay_interval_sec', 'daily_cost'));
		return $daily_cost;
	}

	public function PayIntervalInSeconds(&$plan)
	{
		if (!is_array($plan) || empty($plan)) {
			return 0;
		}

		if (!isset($plan['pay_interval'])) {
			return 0;
		}

		switch ($plan['pay_interval']) {
			case 'weekly':
				return 3600 * 24 * 7;
			case 'biweekly':
				return 3600 * 24 * 14;
			case 'monthly':
				return 3600 * 24 * 30;
			case 'annually':
				return 3600 * 24 * 365.25;
		}

		return 0;
	}


	public function SetPacks($packs)
	{
		$this->packs = $packs;
	}

	public function SetTrans($trans)
	{
		$this->trans = $trans;
	}

	public function SetPlans($plans)
	{
		$this->plans = $plans;
	}

	public function GetPacks($packs)
	{
		return $this->packs;
	}

	public function GetTrans($trans)
	{
		return $this->trans;
	}

	public function GetPlans($plans)
	{
		return $this->plans;
	}

	protected function calcPaymentsBalance($now)
	{
		$total = 0;
		foreach ($this->trans as $tran) {

			$createdtime = @strtotime($tran['createdtime']);
			if ($createdtime > $now) continue; //do not account future payments

			//signup(subscr_signup), payment, cancel(subscr_cancel, subscr_eot), refund (Reversed,Reversal,Refunded)  
			$amount = $tran['payment_amount'];
			switch ($tran['txn_type']) {
				case 'payment':
				case '':
					$total += abs($amount);
					break;
				case 'refund':
					$total -= abs($amount);
					break;
				case 'manual':
					$total += $amount; //sign of amount cn play the role
					break;

			}
		}

		return $total;
	}

	protected function calcPackUsage($now)
	{
		$week_duration   = 3600 * 24 * 7;
		$biweek_duration = 2 * 3600 * 24 * 7;
		$month_duration  = 30 * 3600 * 24;
		$year_duration  = 365.25 * 3600 * 24;

		$balance = 0;
		foreach ($this->plans as $plan) {
			// Free package so return with 0 balance
			if($plan['cost_weekly'] == 0 && $plan['cost_biweekly'] == 0 && $plan['cost_monthly'] == 0 && $plan['cost_annually'] == 0) {
				return $balance;
			}
			$start_date = $plan['start_date'];
			$end_date   = !empty($plan['end_date']) ? $plan['end_date'] : $now;

			//detect pay interval and its cost
			$interval           = $plan['pay_interval'];
			$extradomains_count = $plan['extradomains_count'];
			if (!$plan['fixed'] && isset($this->packs[$plan['pack_id']])) {
				$pack              = $this->packs[$plan['pack_id']];
				$interval_cost     = $pack[$interval];
				$extradomains_cost = $pack['extradomains_' . $interval];
			} else {
				$interval_cost     = $plan['cost_' . $interval];
				$extradomains_cost = isset($plan['extradomains_' . $interval]) ? $plan['extradomains_' . $interval] : 0;
			}

			//calc actual amount
			$suspensions  = $this->calcSuspensions($plan['suspensions'], $start_date, $end_date);
			$duration_sec = $end_date - $start_date - $suspensions;

			switch ($interval) {
				case 'weekly':
					$intervals_elapsed = $duration_sec / $week_duration;
					break;
				case 'biweekly':
					$intervals_elapsed = $duration_sec / $biweek_duration;
					break;
				case 'monthly':
					$intervals_elapsed = $duration_sec / $month_duration;
					break;
				case 'annually':
					$intervals_elapsed = $duration_sec / $year_duration;
					break;
				default:
					throw new Exception('Unknown interval: ' . $interval);

			}

			//<-                calc main cost        ->    <-                    calc extra cost                      ->
			$amount = $intervals_elapsed * $interval_cost + $intervals_elapsed * $extradomains_count * $extradomains_cost;

			$balance += $amount;

//			print_r(compact('start_date', 'end_date', 'interval', 'interval_cost', 'duration_sec', 'suspensions', 'intervals_elapsed', 'amount'));

		}

		return $balance;
	}

	protected function calcSuspensions(&$suspensions, $start_date, $end_date)
	{
		if (!is_array($suspensions))
			return 0;

		$susp = 0;
		foreach ($suspensions as $s) {
			if ($s['start'] >= $start_date && ($s['end'] <= $end_date || empty($s['end']))) {
				$susp += (empty($s['end']) ? $end_date : $s['end']) - $s['start'];
			}
		}

		return $susp;
	}
}

