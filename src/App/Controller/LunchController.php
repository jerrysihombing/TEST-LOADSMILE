<?php
// src/App/Controller/LunchController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class LunchController
{           
    // return array of json recipe data
    private function readRecipeData() {
        $data = file_get_contents('../src/app/Recipe/data.json');           
        // $data = file_get_contents('recipe.json');
        $arr_data = json_decode($data);

        return $arr_data;                
    }

    // return array of json ingredient data
    private function readIngredientsData() {
        $data = file_get_contents('../src/app/Ingredient/data.json');        
        // $data = file_get_contents('ingredient.json');
        $arr_data = json_decode($data);

        return $arr_data;                
    }

    // return ingredient date
    private function getIngredientsDate($title, $type = 'best-before') {
        $arr_data = $this->readIngredientsData();
        
        $result = null;
        foreach($arr_data->ingredients as $item) {
            if ($item->title == $title) {
                if (isset($item->$type)) {
                    $result = $item->$type;
                }
                break;
            }
        }      

        return $result;
    }

    /**
      * @Route("/lunch")
      */
    
    // #0, return array of all recipe
    public function lunch() {       
        $data = $this->readRecipeData();
        
        return new JsonResponse($data);
    }

    /**
      * @Route("/recipe/{ing}")
      */

    // #1, return array of recipe by ingredient
    public function getRecipeByIngredient($ing) {
        $data = $this->readRecipeData();

        $result = array();
        foreach($data->recipes as $item) {        
            foreach($item->ingredients as $ingredient) {
                if ($ingredient == $ing) {
                    $result[] = $item->title;
                }
            }            
        }

        return new JsonResponse($result);        
    }

    /**
      * @Route("/recipe/use-by/{date}")
      */

    // #2, return recipe before ingredient use by date passed
    public function getRecipeBeforeIngredientUseByDate($date) {
        $data = $this->readRecipeData();
        
        $result = array();
        foreach($data->recipes as $item) {
            $include = true;
            foreach($item->ingredients as $ingredient) {                
                $use_by_date = $this->getIngredientsDate($ingredient, 'use-by');
                if ($date >= $use_by_date) {
                    $include = false;                    
                }
            }

            if ($include) {
                $result[] =  $item->title;
            }            
        }
                
        return new JsonResponse($result);
    }

    /**
      * @Route("/recipe/use-by/after-best/{date}")
      */
      
    // #3, return recipe before ingredient use by date passed, including ingredient that passed its best before date
    public function getRecipeAfterIngredientBestBeforeInUseByDate($date) {
        $data = $this->readRecipeData();
        
        $holder = array();
        $temp = array();
        foreach($data->recipes as $item) {
            $include = true;
            $after_best_before = false;
            foreach($item->ingredients as $ingredient) {                
                $best_before_date = $this->getIngredientsDate($ingredient);
                $use_by_date = $this->getIngredientsDate($ingredient, 'use-by');
                if ($date >= $use_by_date) {
                    $include = false;                    
                }
                else {
                    if ($date >= $best_before_date) {
                        $after_best_before = true;                    
                    }
                }
            }

            if ($include) {
                if ($after_best_before) {
                    $temp[] =  $item->title;
                }
                else {
                    $holder[] =  $item->title;
                }                
            }            
        }

        $result = array_merge($holder, $temp);
        
        return new JsonResponse($result);
    }            

}