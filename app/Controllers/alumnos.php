<?php

namespace App\Controllers;

use App\Models\alumno;
use CodeIgniter\HTTP\Response;

class Alumnos extends BaseController
{
    protected $response;

    public function getAlumno($id = null)
    {
        $this->validateSession();
        $alumno = array();
        if (is_numeric($id)) {
            foreach ($this->session->get('alumnosArray') as $key => $val) {
                if ($this->session->get('alumnosArray')[$key]->id == intval($id)) {
                    $alumno[] = $this->session->get('alumnosArray')[$key];
                }
            }
            if (empty($alumno)) {
                $this->response->setStatusCode(404, 'no hay');
                return $this->response->setJSON($alumno);
            } else {
                $this->response->setStatusCode(200, 'se encontró');
                return $this->response->setJSON($alumno);
            }
        }
    }

    public function getAll()
    {
        $this->validateSession();
        $this->response->setStatusCode(200, 'Todos los alumnos');
        return $this->response->setJSON($this->session->get('alumnosArray'));
    }

    public function createAlumno()
    {
        $this->validateSession();
        $id        = $this->request->getPost("id");
        $nombres   = $this->request->getPost('nombres');
        $apellidos = $this->request->getPost('apellidos');
        $matricula = $this->request->getPost('matricula');
        $promedio  = $this->request->getPost('promedio');
        if ($this->validateFields($id,$nombres,$apellidos,$matricula,$promedio)) {
            $alumno = new alumno(
                $id,
                $nombres,
                $apellidos,
                $matricula,
                $promedio
            );
            $newArray   = $this->session->get('alumnosArray');
            $newArray[] = $alumno;
            $this->session->set('alumnosArray', $newArray);
            $this->response->setStatusCode(201, 'Alumno creado');
            return $this->response->setJSON($alumno);
        }else{
            return $this->response->setStatusCode(400, 'Datos erroneos');
        }
    }
    private function validateFields($id,$nombres,$apellidos,$matricula,$promedio){
        if (empty($nombres) || empty($apellidos)) {
            return false;
        }
        if (!is_numeric($id)) {
            return false;
        }
        if (!preg_grep("/(A)[0-9]*/", array($matricula))) {
            return false;
        }
        if (empty($promedio) || !is_numeric($promedio)) {
            return false;
        }
        return true;
    }
    public function updateAlumno($id = null)
    {
        $this->validateSession();
        $alumno = array();
        if (is_numeric($id)) {
            foreach ($this->session->get('alumnosArray') as $key => $val) {
                if ($this->session->get('alumnosArray')[$key]->id == intval($id)) {
                    if (!empty($this->request->getVar('nombres')) && !is_null($this->request->getVar('nombres'))) {
                        $this->session->get('alumnosArray')[$key]->nombres = $this->request->getVar('nombres');
                    }
                    if (!empty($this->request->getVar('apellidos')) && !is_null($this->request->getVar('apellidos'))) {
                        $this->session->get('alumnosArray')[$key]->apellidos = $this->request->getVar('apellidos');
                    }
                    if (!empty($this->request->getVar('matricula'))) {
                        if (preg_grep("/(A)[0-9]*/", array($this->request->getVar('matricula')))) {
                            $this->session->get('alumnosArray')[$key]->matricula = $this->request->getVar('matricula');
                        }else{
                            $this->response->setStatusCode(400, 'error');
                            return $this->response->setJSON($alumno);
                        }
                    }
                    if (!empty($this->request->getVar('promedio'))) {
                        $this->session->get('alumnosArray')[$key]->promedio = $this->request->getVar('promedio');
                    }
                    $alumno[] = $this->session->get('alumnosArray')[$key];
                }
            }
            if (empty($alumno)) {
                $this->response->setStatusCode(404, 'no hay');
                return $this->response->setJSON($alumno);
            } else {
                $this->response->setStatusCode(200, 'se actualizó');
                return $this->response->setJSON($alumno);
            }
        }
    }

    public function deleteAlumno($id = null)
    {
        $this->validateSession();
        $finalArray = array();
        if (is_numeric($id)) {
            foreach ($this->session->get('alumnosArray') as $key => $val) {
                if ($this->session->get('alumnosArray')[$key]->id != intval($id)) {
                    $finalArray[] = $this->session->get('alumnosArray')[$key];
                }
            }
            if (count($finalArray) != count($this->session->get('alumnosArray'))) {
                $this->session->set('alumnosArray',$finalArray);
                return $this->response->setStatusCode(200, 'se actualizó');
            }else{
                return $this->response->setStatusCode(404, 'no se encontró');
            }
        }
    }

    private function validateSession()
    {
        if (!isset($this->session->alumnosArray)) {
            // session has not started so start it
            $this->session->start();
            $this->session->set('alumnosArray', array());
            $this->session->set('profesoresArray', array());
        }
    }
}
