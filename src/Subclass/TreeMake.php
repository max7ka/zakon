<?php
namespace App\Subclass;

class TreeMake{
    public $elements = array();
    //public $text = array();
    public $BR = "<br/>";
    
    public function addElement ($ch){
        
        //$this->elements = array();
        
        /* @var $ch TreeElement*/
        if ($ch->razdel!==null && $ch->glava===null && $ch->statia===null){
            //Добавляем главу
            if (count($this->elements)==0){
                $this->elements[] = $ch;
            }else{
                //ищем последний раздел
                //$findelem=null;
                $curelemPos=0;
                $findelemPos=null;;
                foreach ($this->elements as $elem){
                    /* @var $elem TreeElement*/
                    if ($elem->razdel!==null && $elem->glava===null && $elem->statia===null){
                        //$findelem = $elem;
                        $findelemPos = $curelemPos;
                    }
                    $curelemPos++;
                }
                if ($findelemPos!==null){
                    $ch->prev = $findelemPos;
                    $ch->parent = $this->elements[$findelemPos]->parent;
                    $this->elements[$findelemPos]->next = count($this->elements);
                }
                $this->elements[] = $ch;
            }
        }else{
            if ($ch->razdel!==null && $ch->glava!==null && $ch->statia===null){
                //Ищем главу и последнюю главу
                //$findelem=null;
                $curelemPos=0;
                $findelemPos=null;
                $findRazdelPos=null;
                foreach ($this->elements as $elem){
                    /* @var $elem TreeElement*/
                    if ($ch->razdel===$elem->razdel){
                        if ($elem->razdel!==null && $elem->glava===null && $elem->statia===null){$findRazdelPos = $curelemPos;}
                        if ($elem->razdel!==null && $elem->glava!==null && $elem->statia===null){
                            //$findelem = $elem;
                            $findelemPos = $curelemPos;
                        }
                    }
                    $curelemPos++;
                }
                
                if ($findRazdelPos!==null){
                    $ch->prev = $findelemPos;
                    $ch->parent = $findRazdelPos;
                    if ($this->elements[$findRazdelPos]->firstChild===null){$this->elements[$findRazdelPos]->firstChild=count($this->elements);}
                    if ($findelemPos!==null){
                        $this->elements[$findelemPos]->next = count($this->elements);
                    }
                    $ch->razdel = $ch->razdel;
                    $this->elements[] = $ch;
                }else{
                    //$findelemPos==null раздел не найден
                    //документ без разделов
                    $ch->prev = $findelemPos;
                    if ($findelemPos!==null){
                        $this->elements[$findelemPos]->next = count($this->elements);
                    }
                    $ch->razdel = $ch->razdel;
                    $this->elements[] = $ch;
                }
            }else{
                if ($ch->razdel!==null && $ch->glava!==null && $ch->statia!==null){
                    //ищем главу раздел и оплседнюю статью
                    //$findelem=null;
                    $curelemPos=0;
                    $findelemPos=null;
                    //$findGlavaPos=null;
                    $findGlavaPos=null;
                    foreach ($this->elements as $elem){
                        /* @var $elem TreeElement*/
                        if ($ch->razdel===$elem->razdel && $ch->glava===$elem->glava){
                            //if ($elem->glava!==null && $elem->razdel===null && $elem->statia===null){$findGlavaPos = $curelemPos;}
                            if ($elem->razdel!==null && $elem->glava!==null && $elem->statia===null){$findGlavaPos = $curelemPos;}
                            if ($elem->razdel!==null && $elem->glava!==null && $elem->statia!==null){
                                //$findelem = $elem;
                                $findelemPos = $curelemPos;
                            }
                        }
                        $curelemPos++;
                    }
                    $ch->prev = $findelemPos;
                    $ch->parent = $findGlavaPos;
                    if ($this->elements[$findGlavaPos]->firstChild===null){$this->elements[$findGlavaPos]->firstChild=count($this->elements);}
                    if ($findelemPos!==null){
                        $this->elements[$findelemPos]->next = count($this->elements);
                    }
                    $ch->razdel = $ch->razdel;
                    $this->elements[] = $ch;
                }
            }
        }
    }
    
    public function replaceTextOnLastNode($text){
        if (count($this->elements)>0 && mb_strlen($text)>0){
            
            $LastIsBr = $this->elements[ count($this->elements)-1 ]->text;
            $LastIsBr = mb_substr($LastIsBr, mb_strlen($LastIsBr)-mb_strlen($this->BR));
            $LastIsBr = ($LastIsBr==$this->BR?TRUE:FALSE);
            
            $this->elements[ count($this->elements)-1 ]->text .= ( (!$LastIsBr && mb_strlen($this->elements[ count($this->elements)-1 ]->text)>0 ) ? $this->BR:"").$text;
        }
    }
    
    private function getArrayNode($index){
        $pos = $index;//===0? 0 : $this->elements[$index]->firstChild;
        if ($pos!==null){
            $retArray = array();
            while ($pos>=0){ //for ($i = $pos; $i < count($this->elements); $i++)
                $retArray[] = array(
                    "id" => $this->elements[$pos]->id,
                    "label" => $this->elements[$pos]->label,
                    "children" => $this->getArrayNode( $this->elements[$pos]->firstChild ),
                    "text" => $this->elements[$pos]->text,
                );
                $pos = $this->elements[$pos]->next;
                if ( $pos===null ){
                    break;
                }
            }
            return $retArray;
        }else{
            return array();
        }
    }
    
    public function convertToTreeArray(){
        //устанавливаем id
        $i = 0;
        foreach ($this->elements as $elem){
            $i++;
            $elem->id = $i;
        }
        return  $this->getArrayNode(0);
    }
    
    private function HtmlFromTreeNode($index){
        $pos = $index;
        if ($pos!==null){
            $retHtml = "";
            while ($pos>=0){
                $htype = 2;
                if ($this->elements[$pos]->razdel!==null && $this->elements[$pos]->glava!==null && $this->elements[$pos]->statia===null){$htype = 3;}
                if ($this->elements[$pos]->razdel!==null && $this->elements[$pos]->glava!==null && $this->elements[$pos]->statia!==null){$htype = 4;}
                $retHtml .=
                    '<h'.$htype.'><a name="'. $this->elements[$pos]->id .'"></a>' . $this->elements[$pos]->label . "</h".$htype.">" .
                    "<div>" . $this->elements[$pos]->text . "</div>" . $this->HtmlFromTreeNode($this->elements[$pos]->firstChild);
                /*$retArray[] = array(
                    "id" => $this->elements[$pos]->id,
                    "label" => $this->elements[$pos]->label,
                    "children" => $this->getArrayNode( $this->elements[$pos]->firstChild ),
                    "text" => $this->elements[$pos]->text,
                );*/
                $pos = $this->elements[$pos]->next;
                if ( $pos===null ){
                    break;
                }
            }
            //устанавливаем id
            /*$i = 0;
            foreach ($this->elements as $elem){
                $i++;
                $elem->id = $i;
            }*/
            return $retHtml;
        }else{
            return "";
        }
    }
    public function HtmlFromTree(){
        return $this->HtmlFromTreeNode(0);
    }
}