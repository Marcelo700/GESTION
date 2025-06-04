<?php
class EtiquetasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getEtiquetas()
    {
        $sql = "SELECT * FROM etiquetas ORDER BY nombre ASC";
        return $this->selectAll($sql);
    }

    public function getEtiqueta($id)
    {
        $sql = "SELECT * FROM etiquetas WHERE id = $id";
        return $this->select($sql);
    }

    public function registrar($nombre, $color)
    {
        $sql = "INSERT INTO etiquetas (nombre, color) VALUES (?,?)";
        $datos = array($nombre, $color);
        return $this->insertar($sql, $datos);
    }

    public function modificar($nombre, $color, $id)
    {
        $sql = "UPDATE etiquetas SET nombre=?, color=? WHERE id=?";
        $datos = array($nombre, $color, $id);
        return $this->save($sql, $datos);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM etiquetas WHERE id = ?";
        $datos = array($id);
        return $this->save($sql, $datos);
    }

    public function asignarEtiqueta($id_etiqueta, $id_archivo = null, $id_carpeta = null)
    {
        $sql = "INSERT INTO etiqueta_relaciones (id_etiqueta, id_archivo, id_carpeta) VALUES (?,?,?)";
        $datos = array($id_etiqueta, $id_archivo, $id_carpeta);
        return $this->insertar($sql, $datos);
    }

    public function quitarEtiqueta($id_relacion)
    {
        $sql = "DELETE FROM etiqueta_relaciones WHERE id = ?";
        $datos = array($id_relacion);
        return $this->save($sql, $datos);
    }

    public function getEtiquetasArchivo($id_archivo)
    {
        $sql = "SELECT e.*, er.id as id_relacion FROM etiquetas e 
                INNER JOIN etiqueta_relaciones er ON e.id = er.id_etiqueta 
                WHERE er.id_archivo = $id_archivo";
        return $this->selectAll($sql);
    }

    public function getEtiquetasCarpeta($id_carpeta)
    {
        $sql = "SELECT e.*, er.id as id_relacion FROM etiquetas e 
                INNER JOIN etiqueta_relaciones er ON e.id = er.id_etiqueta 
                WHERE er.id_carpeta = $id_carpeta";
        return $this->selectAll($sql);
    }

    public function getEtiquetaPorNombre($nombre)
    {
        $sql = "SELECT * FROM etiquetas WHERE nombre = ?";
        $datos = array($nombre);
        return $this->select($sql, $datos);
    }
}
?> 