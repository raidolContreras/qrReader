<?php 

class FormsController {
    public static function getStudentData($matricula) {
        return FormsModel::mdlGetDataStudent($matricula);
    }
}