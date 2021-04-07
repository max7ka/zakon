<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;

//use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use App\Subclass\TreeElement;
use App\Subclass\TreeMake;

class DocxParserController extends AbstractController
{
    
    public $json = "";
    /* @var $tree TreeMake */
    //public $tree = null;
    
    /*
      @Route("/tree", name="tree")
     */
    /*public function tree(){
        
        $tm = new TreeMake();
        $te1 = new TreeElement();
        $te1->razdel = "Раздел 1";
        $tm->addElement($te1);
        
        $te2 = new TreeElement();
        $te2->razdel = "Раздел 2";
        $tm->addElement($te2);
            $te21 = new TreeElement();
            $te21->razdel = "Раздел 2";
            $te21->glava = "Глава 1";
            $tm->addElement($te21);
            $te22 = new TreeElement();
            $te22->razdel = "Раздел 2";
            $te22->glava = "Глава 2";
            $tm->addElement($te22);
            $te23 = new TreeElement();
            $te23->razdel = "Раздел 2";
            $te23->glava = "Глава 3";
            $tm->addElement($te23);
                $te231 = new TreeElement();
                $te231->razdel = "Раздел 2";
                $te231->glava = "Глава 3";
                $te231->statia = "Статья 1";
                $tm->addElement($te231);
                $te232 = new TreeElement();
                $te232->razdel = "Раздел 2";
                $te232->glava = "Глава 3";
                $te232->statia = "Статья 2";
                $tm->addElement($te232);
            $te24 = new TreeElement();
            $te24->razdel = "Раздел 2";
            $te24->glava = "Глава 4";
            $tm->addElement($te24);
        $te3 = new TreeElement();
        $te3->razdel = "Раздел 3";
        $tm->addElement($te3);
        
        dump($tm);
        
        return $this->render('docx_parser/index.html.twig', [
            'name' => 'test tree',
        ]);
    }*/
    
    
    /**
     * @Route("/index2", name="index2")
     */
    public function index2(){
        return $this->render('docx_parser/index.html.twig', [
            'name' => 'docx_parser/index.html.twig',
        ]);
    }
    
        
    // между строками оставлять только один /n
    private function ConcatParagraph($s1,$s2) {
        //dump( "<".(mb_substr($s1, mb_strlen($s1)-1))."><".mb_substr($s2,0,1).">" );
        /*return 
        (mb_substr($s1, mb_strlen($s1)-1)=="\n" && mb_substr($s2,0,1)=="\n") ? 
        $s1.mb_substr($s2,1):
        $s1.$s2;*/
        if (mb_substr($s1, mb_strlen($s1)-1)=="\n" && mb_substr($s2,0,1)=="\n"){
            return $s1.mb_substr($s2,1);
        }
        if (mb_substr($s1, mb_strlen($s1)-1)!="\n" && mb_substr($s2,0,1)!="\n"){
            if (mb_strlen($s1)>0){return $s1." ".$s2;}else{return $s2;}
        }
        return $s1.$s2;
    }
    private function ClearParagraph($s){
        $s = ( mb_substr($s, mb_strlen($s)-1)=="\n" ) ? mb_substr($s,0,mb_strlen($s)-1) : $s;
        $s = ( mb_substr($s,0,1)=="\n" ) ? mb_substr($s,1) : $s;
        return $s;
    }
    
    /**
     * @Route("/docx/parser/{docfile}", name="docxparser")
     */
    public function docxparser($docfile)
    {
        
        
        if ($docfile=="*"){
            //https://expange.ru/e/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D1%84%D0%B0%D0%B9%D0%BB%D0%BE%D0%B2_%D0%B2_%D0%BF%D0%B0%D0%BF%D0%BA%D0%B5_(PHP)
            $files = scandir($this->getParameter("path_docx"));
            unset($files[0],$files[1]);
            return new JsonResponse( $files );
        }
        
        //return new Response("TEST DOCX PARSER");
        
        /*dump( $this->ConcatParagraph("123\n","\n456") );
        dump( $this->ConcatParagraph("\n123\n","\n456\n") );
        dump( $this->ConcatParagraph("\n123","\n456\n") );
        dump( $this->ConcatParagraph("\n123\n","456") );*/
        /*dump ( $this->ClearParagraph("\n123\n") );
        dump ( $this->ClearParagraph("123\n") );
        dump ( $this->ClearParagraph("\n123") );*/
        
        $dump = "";
        
        $tm = new TreeMake();
        
        /*$jsonar = array(
            "0" => array(
                "id" => 1,
                "label" => "Level 1",
                "children" => array()
            ),
            "1" => array(
                "id" => 2,
                "label" => "Level 2",
                "children" => array()
            ),
            "2" => array(
                "id" => 3,
                "label" => "Level 3",
                "children" => array()
            )
        );
        
        $this->json = json_encode($jsonar);
        dump($this->json);*/
        
        $fileSystem = new Filesystem();
        $pathtemp = $fileSystem->tempnam( $this->getParameter("path_temp") , "parsingdocx0000.json" );
        //dump($this->getParameter("path_temp"));
        //dump($pathtemp);
        
        //$file = new File($pathfile);
        
        $pathDocx = $this->getParameter("path_docx").$docfile;//"ЗАКОН_О_НОТАРИАТЕ.docx";

        //https://webformyself.com/phpword-chtenie-msword-dokumentov-sredstvami-php/
        /* @var $objReader \PhpOffice\PhpWord\IOFactory */
        $objReader = IOFactory::createReader('Word2007');
        /* @var $phpWord \PhpOffice\PhpWord\PhpWord */
        $phpWord = $objReader->load($pathDocx); 

        //dump($phpWord);
        
        $i=0;

        $TextOther="";
        
        $NazvanRazd = "";
        $isRazdel = FALSE;//для определения названия раздела на вторм цикле, т.к. название на новой строке
        
        $BR = "<br/>"; //nl2br - \n to <br/>
        
        $teRa=null;
        $NomerRazd="I";
        $NomerGlav=0;
        
        foreach($phpWord->getSections() as $section) {
            $sarr = $section->getElements();
            
            foreach ($sarr as $e){
                $TextBlock = "";
                
                //пустой перевод строки
                if (get_class($e)== 'PhpOffice\PhpWord\Element\TextBreak'){
                    /* @var $e \PhpOffice\PhpWord\Element\TextBreak */
                    //dump("TextBreak АБЗАЦ");
                    //$TextOther = "1".$TextOther."1".$BR;
                }
                
                //Отдельный абзац
                if (get_class($e)== 'PhpOffice\PhpWord\Element\TextRun'){
                    /* @var $e \PhpOffice\PhpWord\Element\TextRun */
                    $earr = $e->getElements();
                    //dump("TextRun Begin Начало абзаца");
                    foreach ($earr as $ei){
                        if (get_class($ei)== 'PhpOffice\PhpWord\Element\TextBreak'){
                            /* @var $ei \PhpOffice\PhpWord\Element\TextBreak */
                            //dump("TextBreak ei АБЗАЦ");
                            if (mb_strlen($TextOther)>0){
                                $TextOther = $this->ConcatParagraph($TextOther,$BR);
                            }
                        }                        
                        if(get_class($ei)== 'PhpOffice\PhpWord\Element\Text'){
                            /* @var $ei \PhpOffice\PhpWord\Element\Text */
                            //dump("TextRun ei=".$ei->getText());
                            //$TextBlock = $this->ConcatParagraph($TextBlock,$ei->getText());
                            $TextBlock = $TextBlock.$ei->getText();
                        }
                    }
                }
                if (get_class($e)== 'PhpOffice\PhpWord\Element\TextRun'){
                    if (mb_strlen($TextOther)>0){
                        $TextOther = $this->ConcatParagraph($TextOther,$BR);
                    }
                }
                
                //полная строка
                if(get_class($e)== 'PhpOffice\PhpWord\Element\Text'){
                    if (mb_strlen($e->getText())>0){
                        /* @var $e \PhpOffice\PhpWord\Element\Text */
                        //dump('(Text)Новая строка='.$e->getText());
                        if (mb_strlen($TextBlock)>0) {$TextBlock = $this->ConcatParagraph($BR,$e->getText());} else {$TextBlock = $e->getText();}
                        $TextBlock = $this->ConcatParagraph($TextBlock,$BR);
                    }
                }
                
                if (mb_strlen($TextBlock)>0){
                    
                    //$TextBlock = trim($TextBlock);
                    //удаление пробелов в тексте
                    //$TextBlock = preg_replace('/\s+/', ' ', $TextBlock);
                    
                    //Раздел
                    $posR = mb_strpos($TextBlock , "Раздел");
                    if ($posR!==FALSE){
                        $TextBlock = $this->ClearParagraph($TextBlock);
                        $isRazdel = TRUE;
                        $dump .=(mb_strlen($TextOther)>0)?"OtherText = ".$TextOther."\n":"";
                        $dump .= "+Раздел ".$posR."-".$TextBlock;
                        $NomerRazd = $TextBlock;
                        
                        $teRa = new TreeElement();
                        $teRa->razdel = $NomerRazd;
                        //$teRa->text = $TextOther;
                        
                        $tm->replaceTextOnLastNode($TextOther);
                        $TextOther = "";
                    }else{
                        if ($isRazdel){
                            $isRazdel = FALSE;
                            $posR = 0;
                            $TextBlock = $this->ClearParagraph($TextBlock);
                            $NazvanRazd = $TextBlock;
                            $dump.="\n+Раздел название $NazvanRazd\n";
                            
                            $teRa->label = "$NomerRazd. $NazvanRazd";
                            $tm->addElement($teRa);
                        }
                    }
                    
                    $posG = mb_strpos($TextBlock , "Глава");
                    if ($posG!==FALSE){
                        
                        $TextBlock = $this->ClearParagraph($TextBlock);
                        $point = mb_strpos($TextBlock,".",5);
                        $NomerGlavTest = (int)mb_substr($TextBlock,5+1,$point-5-1);
                        if ($NomerGlavTest>0){
                            $NomerGlav = $NomerGlavTest;
                            $NazvanGlav = mb_substr($TextBlock,$point+1+1,mb_strlen($TextBlock)-$point-1-1);
                            $dump .=(mb_strlen($TextOther)>0)?"OtherText = ".$TextOther."\n":"";
                            $dump .= "+Глава [".$posG."] NomerGlav=[".$NomerGlav."] NazvanGlav=[$NazvanGlav] NomerRazd=$NomerRazd\n";
                            
                            $teGl = new TreeElement();
                            $teGl->razdel = $NomerRazd;
                            $teGl->glava = "Глава ".$NomerGlav;
                            $teGl->label = "Глава $NomerGlav. $NazvanGlav";
                            //$teGl->text = $TextOther;
                            $tm->replaceTextOnLastNode($TextOther);
                            $tm->addElement($teGl);
                            
                            $TextOther = "";
                        }else{//просто слова Глава, без номера
                            $posG = FALSE;
                            $tm->replaceTextOnLastNode($TextOther);
                            $TextOther = "";
                        }
                        
                    }
                    
                    $posS = mb_strpos($TextBlock , "Статья");
                    if ($posS!==FALSE){
                        $TextBlock = $this->ClearParagraph($TextBlock);
                        $point = mb_strpos($TextBlock,".",6);
                        if ($point!==FALSE){
                            //Статья 1. название
                            $NomerStat = (int)mb_substr($TextBlock,6+1,$point-6-1);
                            $NazvanStat = mb_substr($TextBlock,$point+1+1,mb_strlen($TextBlock)-$point-1-1);
                        }else{
                            //Статья 1
                            $point = mb_strlen($TextBlock);
                            $NomerStat = (int)mb_substr($TextBlock,6+1,$point-6-1);
                            $NazvanStat = "";
                        }
                        $dump .=(mb_strlen($TextOther)>0)?"OtherText = ".$TextOther."\n":"";
                        $dump .="+Статья [".$posS."] NomerStat=[".$NomerStat."] NazvanStat=[".$NazvanStat."]\n";
                        
                        $teSt = new TreeElement();
                        $teSt->razdel = $NomerRazd;
                        $teSt->glava = "Глава ".$NomerGlav;
                        $teSt->statia = "Статья ".$NomerStat;
                        $teSt->label = "Статья $NomerStat. $NazvanStat";
                        //$teSt->text = $TextOther;
                        $tm->replaceTextOnLastNode($TextOther);
                        $tm->addElement($teSt);
                        
                        $TextOther = "";
                    }
                    
                    //текст
                    if ($posR===FALSE && $posG===FALSE && $posS===FALSE){
                        $TextOther = $this->ConcatParagraph($TextOther , $TextBlock);
                        //$dump .="+++OtherText = ".$TextOther."\n";
                    }else{
                        $TextOther = "";
                    }
                    
                    //dump($TextBlock);
                    $i++;
                    //if ($i>400) {break 2;}
                }
            }
        }
        if (mb_strlen($TextOther)!=0){
            $dump .=(mb_strlen($TextOther)>0)?"+++OtherText = ".$TextOther."\n":"";
            //$tm->replaceTextOnLastNode($TextOther);
        }
        
        //dump($tm);
        //dump($tm->convertToTreeArray());
        
        //$this->json = json_encode ($tm->convertToTreeArray(),JSON_UNESCAPED_UNICODE);
        
        //$tm->replaceTextOnLastNode("TEST TEST");
        
        $this->json = json_encode ($tm->convertToTreeArray());
        $this->tree = $tm;
        //dump($this->json);
        
        #++$fileSystem->dumpFile($pathtemp, $dump);
		
		
        //$fileSystem->dumpFile($pathtemp, $this->json);

        /*$i = 0;
        foreach ($tm->elements as $elem){
            $i++;
            $elem->id = $i;
        }*/
        
        return new JsonResponse(
                array(
                    "json" => $tm->convertToTreeArray(),
                    "html" => $tm->HtmlFromTree()
                )
            );
        
        //return new JsonResponse($tm->convertToTreeArray());
        
        /*return new JsonResponse(
            "{" .
            "json :" . json_encode ($tm->convertToTreeArray(),JSON_UNESCAPED_UNICODE) .",".
            "html :" . json_encode ($tm->HtmlFromTree(),JSON_UNESCAPED_UNICODE) . 
            "}"
        );*/
        
        //return new Response($this->json);
        /*return $this->render('docx_parser/docxparser.html.twig', [
            'controller_name' => 'DocxParserController',
        ]);*/
    }
}
