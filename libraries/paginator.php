<?php

class Paginator {

    private $limit;
    private $page = 1;
    private $last;
    private $result;
    private $array = array();
    private $totalPages;
    private $navLink = array();

    function __construct($array, $limit, $last = 10) {

        $this->limit = $limit;
        $this->array = $array;
        $this->last = $last;
    }

    public function pageCount() {
        for ($i = 1; $i <= $this->totalPages; $i++) {
            $this->navLink[] = $i;
        }

        $t = $this->page * 1;
        $first = $t - 1;

        if ($first == 0) {
            $links = array_slice($this->navLink, $first, $this->last);
        } else {
            if ($first > 10) {

                $links = array_slice($this->navLink, $first, $this->last - 1);
                $first = $first - 10;
                echo "$first";
                $prev = array("$first" => "$first");
                $links = array_merge($prev, $links);
            } else {
                $links = array_slice($this->navLink, $first, $this->last - 1);
                $prev = array("$first" => "$first");
                $links = array_merge($prev, $links);
            }
        }

        return $links;
    }

    public function getPage($page) {
        $this->page = $page;
        $index = ($this->page - 1) * $this->limit;
        $this->result = array_slice($this->array, $index, $this->limit);
        $this->totalPages = ceil(count($this->array) / $this->limit);
        return $this->result;
    }

}
