<?php

class TableService {

    
    public static function make($data, $module){

        $configPath = ROOT_PATH."/app/tables/{$module}.php";

        if(!file_exists($configPath)){
            throw new Exception("No existe configuración de tabla para {$module}");
        }

        $config = require $configPath;

        $table = new TableBuilder($data);

        foreach($config as $col){

            switch($col['type']){

                case 'image':
                    $table->image(
                        $col['folder'],
                        $col['imageField'],
                        $col['textField'],
                        $col['title'],
                        $col['subField'] ?? null,
                    );
                break;

                case 'status':
                    $statuses = require ROOT_PATH.'/app/config/status.php';

                    $table->status(
                        $col['field'], 
                        $col['title'],
                        $statuses[$col['source']] ?? []
                    );
                break;

                case 'date':
                    $table->date($col['field'], $col['title']);
                break;

                case 'text':
                    $table->text($col['field'], $col['title']);
                break;
                case 'actions':
                    $table->actions(
                        $col['title'],
                        $col['actions']
                    );
                break;
            }
        }

        return [
            'columns' => $table->getHeaders(),
            'rows' => $table->build()
        ];
    }

}