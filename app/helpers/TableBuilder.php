<?php

class TableBuilder{

    private $data;
    private $columns = [];
    private $headers = [];
    protected $title = 'Listado';

    public function __construct($data){
        $this->data = $data;
    }

    public function title($title){
        $this->title = $title;
        return $this;
    }

    public function getTitle(){
        return $this->title;
    }

    public function image($folder, $imageField, $textField, $title, $subField = null)
    {
        $this->headers[] = $title;

        $this->columns[] = function($row) use ($folder, $imageField, $textField, $subField){

            $image = $row[$imageField] ?? null;
            $text  = $row[$textField] ?? '';
            $sub   = $subField ? ($row[$subField] ?? '') : '';

            $base = '/afec/public/assets/img/'.$folder.'/';

            $img = $image
                ? $base.$image
                : $base.'default.png';

            $html = '<div class="cell-flex">';

            $html .= '<div class="cell-avatar">';
            $html .= '<img src="'.$img.'" class="avatar">';
            $html .= '</div>';

            $html .= '<div class="cell-text">';

            if($text){
            $html .= '<div class="cell-title">'.$text.'</div>';
            }

            if($sub){
            $html .= '<div class="cell-subtitle">'.$sub.'</div>';
            }

            $html .= '</div>';
            $html .= '</div>';

            return $html;
        };

        return $this;
    }
    
    public function text($field, $title){
        $this->headers[] = $title;
        $this->columns[] = function($row) use ($field){
            return $row[$field] ?? '';
        };

        return $this;
    }

    public function date($field, $title){
        $this->headers[] = $title;
        $this->columns[] = function($row) use ($field){
            if(empty($row[$field])){
                return '-';
            }
            return date('d/m/Y',strtotime($row[$field]));
        };

        return $this;
    }

    public function status($field, $title, $map = []){
        $this->headers[] = $title;

        $this->columns[] = function($row) use ($field, $map){

            $value = $row[$field] ?? null;

            if(isset($map[$value])){
                $item = $map[$value];

                $icon = isset($item['icon']) 
                    ? "<i class='fas fa-{$item['icon']} me-1'></i>" 
                    : '';

                return "
                    <span class='badge bg-{$item['color']} p-2'>
                        {$icon}{$item['label']}
                    </span>
                ";
            }

            return "<span class='badge bg-dark'>Desconocido</span>";
        };

        return $this;
    }

    public function boolean($field,$title){

        $this->headers[] = $title;

        $this->columns[] = function($row) use ($field){

            return $row[$field]
                ? '<span class="badge bg-success">Sí</span>'
                : '<span class="badge bg-danger">No</span>';

        };

        return $this;
    }

    public function badge($field,$title){

        $this->headers[] = $title;

        $this->columns[] = function($row) use ($field){

            $colors = [
                'Activo' => 'success',
                'Inactivo' => 'secondary',
                'Suspendido' => 'danger'
            ];

            $value = $row[$field];
            $color = $colors[$value] ?? 'primary';

            return '<span class="badge bg-'.$color.'">'.$value.'</span>';

        };

        return $this;
    }
    
    public function money($field,$title){

        $this->headers[] = $title;

        $this->columns[] = function($row) use ($field){

            return '$ '.number_format($row[$field],2);

        };

        return $this;
    }

    public function actions($title, $actionsConfig){
        $this->headers[] = $title;

        $this->columns[] = function($row) use ($actionsConfig){

        $html = '<div class="table-actions">';

        foreach ($actionsConfig as $action) {

            // Verificar si la acción tiene una condición para mostrarse
            if (isset($action['show_if'])) {

                $field  = $action['show_if']['field'];
                $values = $action['show_if']['values'];

                if (!in_array($row[$field] ?? null, $values)) {
                    continue;
                }
            }

            $url = $action['url'];

            // reemplazar {id}, {campo}, etc.
            foreach ($row as $key => $value) {
                $url = str_replace('{'.$key.'}', $value, $url);
            }

            $icon  = $action['icon'] ?? 'circle';
            $title = $action['title'] ?? '';
            $class = $action['class'] ?? '';

            $html .= '
                <a  href="'.$url.'"
                    class="btn-icon '.$class.'"
                    title="'.$title.'">
                    <i class="fas fa-'.$icon.'"></i>
                </a>';
        }

            $html .= '</div>';

            return $html;
        };

        return $this;
    }
    
    public function custom($title,$callback){

        $this->headers[] = $title;

        $this->columns[] = $callback;

        return $this;
    }

    public function build(){

        $rows = [];

        foreach($this->data as $row){

            $r = [];

            foreach($this->columns as $col){

                $r[] = $col($row);

            }

            $rows[] = $r;

        }

        return $rows;
    }

    public function getHeaders(){
        return $this->headers;
    }

}

?>