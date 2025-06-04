<?php
class Etiquetas extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id'])) {
            header('Location: ' . BASE_URL);
        }
    }

    public function index()
    {
        $data['title'] = 'Etiquetas';
        $data['script'] = 'etiquetas.js';
        $this->views->getView('etiquetas', 'index', $data);
    }

    public function listar()
    {
        $data = $this->model->getEtiquetas();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['acciones'] = '<div>
                <button class="btn btn-primary" type="button" onclick="editarEtiqueta(' . $data[$i]['id'] . ')">
                    <i class="material-icons">edit</i>
                </button>
                <button class="btn btn-danger" type="button" onclick="eliminarEtiqueta(' . $data[$i]['id'] . ')">
                    <i class="material-icons">delete</i>
                </button>
            </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar()
    {
        $nombre = $_POST['nombre'];
        $color = $_POST['color'];
        $id = $_POST['id'];
        if (empty($nombre) || empty($color)) {
            $res = array('tipo' => 'warning', 'mensaje' => 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            if ($id == '') {
                $data = $this->model->registrar($nombre, $color);
                if ($data > 0) {
                    $res = array('tipo' => 'success', 'mensaje' => 'ETIQUETA REGISTRADA');
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL REGISTRAR');
                }
            } else {
                $data = $this->model->modificar($nombre, $color, $id);
                if ($data == 1) {
                    $res = array('tipo' => 'success', 'mensaje' => 'ETIQUETA MODIFICADA');
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR');
                }
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function editar($id)
    {
        $data = $this->model->getEtiqueta($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar($id)
    {
        $data = $this->model->eliminar($id);
        if ($data == 1) {
            $res = array('tipo' => 'success', 'mensaje' => 'ETIQUETA ELIMINADA');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL ELIMINAR');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function asignarEtiqueta()
    {
        $id_etiqueta = $_POST['id_etiqueta'];
        $id_archivo = !empty($_POST['id_archivo']) ? $_POST['id_archivo'] : null;
        $id_carpeta = !empty($_POST['id_carpeta']) ? $_POST['id_carpeta'] : null;

        if (empty($id_etiqueta) || (empty($id_archivo) && empty($id_carpeta))) {
            $res = array('tipo' => 'warning', 'mensaje' => 'DATOS INCOMPLETOS');
        } else {
            $data = $this->model->asignarEtiqueta($id_etiqueta, $id_archivo, $id_carpeta);
            if ($data > 0) {
                $res = array('tipo' => 'success', 'mensaje' => 'ETIQUETA ASIGNADA');
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL ASIGNAR');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function quitarEtiqueta($id_relacion)
    {
        $data = $this->model->quitarEtiqueta($id_relacion);
        if ($data == 1) {
            $res = array('tipo' => 'success', 'mensaje' => 'ETIQUETA QUITADA');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL QUITAR');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getEtiquetasArchivo($id_archivo)
    {
        $data = $this->model->getEtiquetasArchivo($id_archivo);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getEtiquetasCarpeta($id_carpeta)
    {
        $data = $this->model->getEtiquetasCarpeta($id_carpeta);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
?> 