<?php
use Stripe\Stripe;
require 'vendor/autoload.php';
/*class StripeVerification extends CI_Controller{
    public function verifyTransaction(){
        echo 'Verifying..';
    }
}*/
function verifyTransaction(){
    $CI =& get_instance();
    //echo 'hook-->verifyTransaction..';
    if (property_exists($CI, "createverificationsession_post") && $CI->createverificationsession_post === TRUE)
    {
        // Load `.env` file from the server directory so that
        // environment variables are available in $_ENV or via
        // getenv().
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        $stripe = new \Stripe\StripeClient([
            'api_key' => $_ENV['STRIPE_SECRET_KEY'],
            'stripe_version' => '2020-08-27',
        ]);

        //header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $body = json_decode($input);
        $event = null;

        try {
            // Make sure the event is coming from Stripe by checking the signature header
            $event = \Stripe\Webhook::constructEvent(
                $input,
                $_SERVER['HTTP_STRIPE_SIGNATURE'],
                $_ENV['STRIPE_WEBHOOK_SECRET']
            );
        }
        catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if ($event->type == 'identity.verification_session.verified') {
            // All the verification checks passed
            $verification_session = $event->data->object;
            
            /*ob_start();
            var_dump( $verification_session);
            error_log(ob_get_clean(), 4);*/
          
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $expandedSession = $stripe->identity->verificationSessions->retrieve(
              $verification_session['id'],
              [
                'expand' => [
                  'verified_outputs',
                ],
              ]
            );
          
            //ob_start();
            //var_dump($expandedSession);
            //error_log(ob_get_clean(), 4);

        } elseif ($event->type == 'identity.verification_session.requires_input') {
            # At least one of the verification checks failed
            $verification_session = $event->data->object;
            
            if ($verification_session->last_error->code == 'document_unverified_other') {
                # The document was invalid
            } elseif ($verification_session->last_error->code == 'document_expired') {
                # The document was expired
            } elseif ($verification_session->last_error->code == 'document_type_not_suported') {
                # The document type was not supported
            } else {
                # ...
            }
        }
    }
}
?>