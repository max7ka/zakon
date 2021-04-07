<?php
namespace App\Subclass;

class TreeElement{
    public $id = 0;
    //public $type = 0; // 1 - раздел 2 - глава 3 - статья
    public $razdel = null;
    public $glava = null;
    public $statia = null;
    public $label = null;
    //public $children = null;
    public $parent = null;
    public $prev = null;
    public $next = null;
    public $firstChild = null;
    
    public $text = "";
    
    /* формируем линейный массив
     * у каждого элемента ссылка на родителя, на предидущего дитя и следующего дитя
     * в конце из линейного массива формируем дерево с нужными элементами
     * преобразуем в json
     * */
    
    //получаем массив -> конвертируем в json
    /*public function getElementTreeArray (){
        return array(
            "id" => $this->$id,
            "label" => $this->$label,
            "children" => $this->getChildrensArray()
        );
    }
    public function getChildrensArray() {
        $return_arr = array();
        foreach ($this->$children as $ch){
             @var $ch TreeElement
            $return_arr[] = $ch->getElementTreeArray();
        }
        return $return_arr;
    }*/
}