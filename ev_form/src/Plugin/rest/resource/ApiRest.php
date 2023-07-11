<?php

namespace Drupal\ev_form\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\rest\ModifiedResourceResponse;
use Psr\Log\LoggerInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;

/**
 *
 * @RestResource(
 *   id = "ev_form_resource",
 *   label = @Translation("Apirest table example users"),
 *   uri_paths = {
 *      "canonical" = "/example-module/data/{registro}",
 *      "create" = "/example-module/data/crear/registro"
 *   }
 * )
 */
class Apirest extends ResourceBase {

    /**
   * Metodo get
   * @return \Drupal\rest\ResourceResponse
   */
    public function get($registro = NULL){

        if ($registro == 'all') {
            $conn= Database::getConnection();
            $query = $conn->select('example_users', 'eu');
            $query ->fields('eu', ['registro_id','nombre','identificacion','fecha_nacimiento','cargo','estado']);
            $results = $query->execute()->fetchAll();    
            $tuplas= array();
            foreach ($results as $result=> $value) {
                $tuplas[$value->registro_id]= [
                    'registro_id'=>$value->registro_id,
                    'nombre'=>$value->nombre,
                    'identificacion'=>$value->identificacion,
                    'fecha_nacimiento'=>$value->fecha_nacimiento,
                    'cargo'=>$value->cargo,
                    'estado'=>$value->estado
                ];
            }

            if (!empty($tuplas)) {
              return new ResourceResponse($tuplas);
            }
            throw new NotFoundHttpException(t('No hay registros'));
        }else {
            $conn= Database::getConnection();
            $query = $conn->select('example_users', 'eu');
            $query ->fields('eu', ['registro_id','nombre','identificacion','fecha_nacimiento','cargo','estado']);
            $query->condition('registro_id', $registro);
            $results = $query->execute()->fetchAll();    
            $tuplas= array();
            foreach ($results as $result=> $value) {
                $tuplas[$value->registro_id]= [
                    'registro_id'=>$value->registro_id,
                    'nombre'=>$value->nombre,
                    'identificacion'=>$value->identificacion,
                    'fecha_nacimiento'=>$value->fecha_nacimiento,
                    'cargo'=>$value->cargo,
                    'estado'=>$value->estado
                ];
            }

            if (!empty($tuplas)) {
              return new ResourceResponse($tuplas);
            }
            throw new NotFoundHttpException(t('No hay registros'));
        }
    }

    /**
   * Metodo delete
   */
    public function delete($registro) {
        if(is_numeric($registro)){
            $conn= Database::getConnection();
            $query = $conn->delete('example_users')
            ->condition('registro_id', $registro)
            ->execute();

            if ($query==1) {
                return new ModifiedResourceResponse(NULL, 204);
            }else{
                throw new NotFoundHttpException(t('Internal Server Error'));
            }
        }else {
            throw new NotFoundHttpException(t('Parametro no valido'));
        }
    }


    /**
    * Metodo post
    * @param mixed $data
    *
    * @throws \Symfony\Component\HttpKernel\Exception\HttpException
    *   Throws exception expected.
    */
    public function post($data) {
        if ($data == NULL) {
            throw new BadRequestHttpException('No entity content received.');
        }
        
        foreach ($data as $key => $value) {

            $nombre                 = $value['nombre'];
            $identificacion         = $value['identificacion'];
            $fecha_nacimiento       = ($value['fecha_nacimiento'] != '') ? $value['fecha_nacimiento']: '0000-00-00';
            $cargo                  = ($value['cargo']!='') ? $value['cargo']:0000;
            $estado                 = ($value['cargo']==1) ? $value['cargo']:0;


            $query = \Drupal::database()->insert('example_users');
            $query->fields([
                'nombre','identificacion','fecha_nacimiento','cargo','estado'
            ]);
            $query->values([
                $nombre,$identificacion,$fecha_nacimiento,$cargo,$estado
            ]);
            $query->execute();

        }

        $message = $this->t("Registro guardado con exito");

        return new ResourceResponse($message, 200);
    }

    /**
    * metodo patch
    * @param mixed $data
    *
    * @throws \Symfony\Component\HttpKernel\Exception\HttpException
    */
    public function patch($registro=null, $data){
        if ($data == NULL) {
            throw new BadRequestHttpException('No entity content received.');
        }

        foreach ($data as $key => $value) {
            $nombre                 = $value['nombre'];
            $identificacion         = $value['identificacion'];
            $fecha_nacimiento       = ($value['fecha_nacimiento'] != '') ? $value['fecha_nacimiento']: '0000-00-00';
            $cargo                  = ($value['cargo']!='') ? $value['cargo']:0000;
            $estado                 = ($value['cargo']==1) ? $value['cargo']:0;

            $conn= Database::getConnection();
            $query = $conn->update('example_users');
            $query->fields([
                'nombre'=>$nombre,
                'identificacion'=>$identificacion,
                'fecha_nacimiento'=>$fecha_nacimiento,
                'cargo'=>$cargo,
                'estado'=>$estado
            ]);
            $query->condition('registro_id', $registro , '=');
            $query->execute();
        }

        $message = $this->t("Datos actualizados");
        return new ResourceResponse($message , 200);
    }
}