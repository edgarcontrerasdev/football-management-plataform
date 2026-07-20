<?php
class Pagination{

    public static function paginate($total, $limit = 10){
        $page = $_GET['p'] ?? 1;
        if($page <  1){
            $page = 1;
        }

        $offset = ($page - 1)*$limit;
        $pages = ceil($total/$limit);

        return[
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page,
            'pages' => $pages
        ];

    }
}

?>