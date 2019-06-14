<?php
// ***********************************************************
// name: api_config.php
// date: 2019-05-03
// auth: vvenning
// desc: demo rest api
// *****************************************************

//  salt portion for password encryption
	const PEPPER = 'tqREwsPdswzC8GxO8PQKuJLMr33oWav8E3mPXd2b';


//  email validation regex
	const EMAIL_REGEX		= '^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$';

	const DEBUG_MODE			= true;
	const FATAL_ERROR_FOLDER	= '../../logs';
//  const DIRECTORY_SEPARATOR   = '/';

	if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' )
	{
		define("ENV","local");
		define("DATABASE", "venningdev");
    }
	elseif($_SERVER['HTTP_HOST'] == 'dev-services.trustsecurenow.com')
	{
		define("ENV","dev");
		define("DATABASE", "venningdev");
    }

// Generic Email API *********************************************************************
// Generic API key - You need this to send generic email through COntactUSers
   define("Partners_API_KEY", "q9aZ8D8gMm6OWTPrf8ytbNMlseYvlN1e");
   define("Clients_API_KEY", "7vMbrBcTByZH7E4BJpr4ZdyAOOXi8jbr");
   define("Users_API_KEY", "8jZWO2vM9GOwR8HKF376s69EVscY1AtC");
   define("Email_API_KEY", "xBuAIhSB5OvRmKhx6boX8YV2IP2x3mGb");


// Contact Users **************************************************************************************************
   define("Email_SENDER", "No-reply@business-relationships.com");
   define("Email_SENDER_NAME", "Business Relationships");
   define("Email_SUBJECT", "Relationship Tip - ");

//  Database table names
    define("USERS_TABLE","users");
	define("CLIENTS_TABLE","clients");
	define("PARTNERS_TABLE","partners");
	define("PARTNER_MESSAGES_TABLE","partner_messages");
	define("PARTNER_MESSAGE_TYPES_TABLE","partner_message_types");
    define("TAGS_TABLE","tags");
    define("ERROR_MESSAGSES_TABLE","error_messages");

    define("CHARGIFY_CUSTOMERS_TABLE", "chargify_customers");
    define("CHARGIFY_EVENTS_TABLE","chargify_events");
    define("CHARGIFY_PAYMENT_PROFILES_TABLE","chargify_payment_profiles");
    define("CHARGIFY_PRODUCTS_TABLE","chargify_products");
    define("CHARGIFY_SITES_TABLE","chargify_sites");
    define("CHARGIFY_STATEMENTS_TABLE","chargify_statements");
    define("CHARGIFY_SUBSCRIPTIONS_TABLE","chargify_subscriptions");
    define("CHARGIFY_TRANSACTIONS_TABLE","chargify_transactions");
    define("CHARGIFY_FUTURE_PAYMENTS_TABLE","chargify_future_payments");
    define("CHARGIFY_CREDIT_CARDS_TABLE","chargify_credit_cards");

//	user groups
    define("END_USERS","Members");
    define("MANAGERS","Managers");
	
	define("REST_API_DATABASE_ERROR_LOG_PATH","/REST_APIs/logs/database_error.txt");


?>
