<?php
/**
 * @file
 * Contains Drupal\dictionary_module\DictionaryModuleForm
 */
namespace Drupal\dictionary_module\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Component\Serialization\Json;

class DictionaryModuleForm extends FormBase {
  public function getFormId() {
	return 'dictionary_module_form';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
	$form['#prefix'] = '<div id="was-page-useful-modal-form">';
    $form['#suffix'] = '</div>';
	$form['text_input'] = array(
  	'#type' => 'textfield',
  	'#title' => 'Enter text',
  	'#description' => 'Please enter the text for defination'
  	
	);
	$form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Find meaning'),
	  '#ajax' => array(
    	// Function to call when event on form element triggered.
    	//'callback' => 'Drupal\dictionary_module\Form\DictionaryModuleForm::definationValidateCallback',
		'callback' => [$this, 'definationValidateCallback'],
		'event' => 'click',
		'progress' => 'throbber',
  	),

    );
	return $form;
  }
 
  public function definationValidateCallback(array &$form, FormStateInterface $form_state) {

	// Check if user entered value.
    if (!empty($form_state->getValue('text_input'))) {
	  $text_input = $form_state->getValue('text_input');
	  $client = new \GuzzleHttp\Client();
	  $url = 'https://dictionaryapi.dev/';
	  $request = $client->request('GET',$url, ['verify' => false]);

	  $client = \Drupal::httpClient();
	  $request = $client->get('https://api.dictionaryapi.dev/api/v2/entries/en/'.$text_input);
	  $get_data = $request->getBody()->getContents();

	  // Instantiate an AjaxResponse Object to return from .
	  $ajax_response = new AjaxResponse();
	  $json = Json::decode($get_data);

	  $ajax_response->addCommand(new ReplaceCommand('#was-page-useful-modal-form', $get_data));
	  // Return the AjaxResponse Object.

      return $ajax_response;
	
	}

  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
	// \Drupal::messenger()->addMessage('To store search terms in near future');
  }

}