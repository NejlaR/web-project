<?php

class BaseService {

    protected $dao;

    public function __construct($dao){
        $this->dao = $dao;
    }

    public function get_all(){
        return [
            "success" => true,
            "data" => $this->dao->get_all()
        ];
    }

    public function get_by_id($id){
        $item = $this->dao->get_by_id($id);

        return [
            "success" => $item ? true : false,
            "data" => $item,
            "message" => $item ? "Record found" : "Record not found"
        ];
    }

    public function add($entity){
        return [
            "success" => true,
            "data" => $this->dao->add($entity),
            "message" => "Record created"
        ];
    }

    public function update($entity, $id, $id_column = "id"){
        return [
            "success" => true,
            "data" => $this->dao->update($entity, $id, $id_column),
            "message" => "Record updated"
        ];
    }

    public function delete($id){
        return [
            "success" => true,
            "data" => $this->dao->delete($id),
            "message" => "Record deleted"
        ];
    }
}
