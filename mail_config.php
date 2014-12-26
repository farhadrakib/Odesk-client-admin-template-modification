<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
/*
 * Email Notifications Config
 */
# Specify email, that will be used as sender for the admin emails for you and your clients
# ! NOTE add this email in white list in order to prevent appearing emails in spam
define('PHP_MAILER_SENDER_EMAIL', 'no-reply@yourdomain.com');
define('PHP_MAILER_SENDER_NAME', 'HMTracker Notifications');

# Specify Mailer Transport type:
# 1 - SMTP
# 2 - Sendmail
# 3 - Mail
define('PHP_MAILER_TRANSPORT', 3);

# Specify Mailer SMTP Settings
define('PHP_MAILER_SERVER', '');
define('PHP_MAILER_PORT', 25);
define('PHP_MAILER_SSL', false);
define('PHP_MAILER_USERNAME', '');
define('PHP_MAILER_PASSWORD', '');
