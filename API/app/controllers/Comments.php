<?php

    require "app/models/CommentsModel.php"; 
    
    class Comments{
        
        private $comments;
        private $isLogged;
        private $isAdmin;
        
        function __construct(){
            $this->comments = new CommentsModel();
            if(isset($_SESSION['isLogged']) && $_SESSION['isLogged'] === true){
                $this->isLogged = true;
            }else{
               $this->isLogged = false; 
            }
            $this->isAdmin = (isset($_SESSION['isLogged']) && isset($_SESSION['role']) && $_SESSION['isLogged'] == true && strtolower($_SESSION['role']) == 'admin' ) ? true : false;
        }
        
        function getAll(){
            
            return $this->comments->selectAll();
        }
        
        function createItem(){
            if($this->isLogged === false){
                return array("success"=>false,"message"=>"You must be logged in to be able to post comments.");
            }
            if(isset($_POST['article_id']) && !empty(trim($_POST['article_id']))){
                
                if(!empty($_POST['title']) && !empty($_POST['content'])){
                    
                    $_POST['user_id'] = $_SESSION['userId'];
                    
                    $dbResult = $this->comments->insertItem($_POST);
                    if($dbResult > 0){
                        return array("success"=>true,"message"=>"Comment succesfully added!");
                    }else{
                        return array("success"=>false,"message"=>"Error while adding comment!");
                    }
                }else{
                    return array("success"=>false,"message"=>"All fields are required!");
                }
            }else{
                return array("success"=>false,"message"=>"Comment could not be added");
            }
            
        }
        
        function getCommentsForArticle(){
            
            if(!empty($_GET["article_id"])){
                
                return $this->comments->getCommentsForArtID($_GET["article_id"]);
            }
            else{
                return "You must specify and article ID!";
            }
        }
        
        function deleteItem(){
            global $REQUEST;
            
            if($this->isLogged === false){
                return array("success"=>false,"message"=>"You must be logged in to delete comments");
            }
            if($this->isAdmin === false){
                
                if(isset($REQUEST['userId']) && isset($_SESSION['userId'])){
                    if($REQUEST['userId'] !== $_SESSION['userId']){
                        return array("success"=>false,"message"=>"You cand delete only you`re own comments");
                    }
                }else{
                       return array("success"=>false,"message"=>"User unknown");
                }
            }
            
            if(isset($REQUEST['id'])){
                
                //get current date
                date_default_timezone_set("Europe/Bucharest");
                
                $DbResult = $this->comments->deleteItem($REQUEST['id'],date("Y-m-d H:i:s"));
                
                if($DbResult['error'] === null){
                     return array("success"=>true,"message"=>"Comment deleted successfully");
                }else{
                    return array("success"=>false,"message"=>"Error deleting comment");
                }
            }else{
                 return array("success"=>false,"message"=>"Comment could not be deleted");
            }
        }
     
        function updateItem(){
            global $REQUEST;
            
            if($this->isLogged === false){
                return array("success"=>false,"message"=>"You must be logged in to modify comments");
            }
            if($this->isAdmin === false){
                
                if(isset($REQUEST['userId']) && isset($_SESSION['userId'])){
                    if($REQUEST['userId'] !== $_SESSION['userId']){
                        return array("success"=>false,"message"=>"You cand modify only you`re own comments");
                    }
                }else{
                       return array("success"=>false,"message"=>"User unknown");
                }
            }
            
            if(isset($REQUEST['id']) && !empty(trim($REQUEST['id']))){
                
                if(isset($REQUEST['title']) && isset($REQUEST['content'])){
                    
                    //get current date
                    date_default_timezone_set("Europe/Bucharest");
                    $REQUEST['last-modified'] = date("Y-m-d H:i:s");
                    $DbResult = $this->comments->updateItem($REQUEST);
                    if($DbResult['error'] === null){
                        return array("success"=>true,"message"=>"Comment deleted successfully");
                    }else{
                        return array("success"=>false,"message"=>"Error deleting comment");
                    }
                }else{
                    return array("success"=>false,"message"=>"All fields are required!");
                }
            }else{
                 return array("success"=>false,"message"=>"Comment could not be updated");
            }
        }
    }
    


?>