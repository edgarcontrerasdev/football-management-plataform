<?php

    function redirect(string $url)
    {
        header("Location: {$url}");
        exit;
    }

    function redirectRoute(string $page, array $params = [])
    {
        $query = http_build_query(
            array_merge(['page' => $page], $params)
        );

        redirect("index.php?{$query}");
    }

?>