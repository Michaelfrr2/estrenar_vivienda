<?php

namespace Drupal\ev_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Messenger\MessengerInterface;

class ev_form extends FormBase {
    public function getFormId() {
        return 'ev_form';
      }

    public function buildForm(array $form, FormStateInterface $form_state) {


        $form['titulo'] = [
            '#type' => 'markup',
            '#markup' => '<h1>'.t('Formulario de registro').'</h1>',
        ];
        $form['nombre'] = array(
            '#type' => 'textfield',
            '#title' => t('Nombre'),
            '#required' => TRUE,
            '#description' => t('Por favor escribir nombres completos'), 
        );
        $form['identificacion'] = array(
            '#type' => 'number',  
            '#title' => t('Identificacion'),
            '#required' => TRUE,
            '#description' => t('Numero de identificacion'), 
        );

        $form['nacimiento'] = array(
            '#title' => t('Fecha de nacimiento'),
            '#type' => 'date',
            '#description' => t('Seleccione fecha de nacimiento 0000-00-00'),
        );


        $conn= Database::getConnection();
        $query = $conn->select('example_cargo', 'ec');
        $query ->fields('ec', ['cargo_id','nombre']);
        $results = $query->execute()->fetchAll();
        $cargo[''] = t('Seleccionar');

        foreach ($results as $result=> $value) {
            $cargo[$value->cargo_id]=$value->nombre;     
        }

        $form['cargo'] = [
            '#type' => 'select',
            '#title' => t('Cargo'),
            '#options' => $cargo ,
            '#description' => t('Seleccione cargo'),
        ];

        $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        ];

        $form['#theme'] = 'twig-formulario-form';
        $form['#attached']['library'][] = 'ev_form/ev_form';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (strlen($form_state->getValue('identificacion')) < 6) {
            $form_state->setErrorByName('identificacion', $this->t('El minimo de caracteres es 6 para numero de identificacion.'));
        }
        if (strlen($form_state->getValue('identificacion')) > 12) {
            $form_state->setErrorByName('identificacion', $this->t('El maximo de caracteres es 12 para numero de identificacion.'));
        }
        if (!preg_match('/^[a-zA-Z0-9]+/', $form_state->getValue('nombre'))) {
            $form_state->setErrorByName('nombre', $this->t('El campo nombre no debe contener caracteres especiales.'));
          }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $nombre=$form_state->getValue('nombre');
        $identificacion=$form_state->getValue('identificacion');
        $fecha_nacimiento = ($form_state->getValue('nacimiento')!=='') ? $form_state->getValue('nacimiento') : '0000-00-00' ;
        $cargo = ($form_state->getValue('cargo') !='') ? $form_state->getValue('cargo') : '0000' ;
        $estado =($form_state->getValue('cargo') == 1) ? 1 : 0 ;

        $query = \Drupal::database()->insert('example_users');
        $query->fields([
            'nombre','identificacion','fecha_nacimiento','cargo','estado'
        ]);
        $query->values([
            $nombre,$identificacion,$fecha_nacimiento,$cargo,$estado
        ]);
        $query->execute();

        \Drupal::messenger()->addMessage(t('Registro guardado con exito.'), MessengerInterface::TYPE_STATUS, TRUE);
    }
    
}