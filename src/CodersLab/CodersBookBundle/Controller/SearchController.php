<?php

namespace CodersLab\CodersBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use CodersBookBundle\Entity\PersonRepository;
use CodersBookBundle\Entity\CLGroupRepository;

class SearchController extends Controller {
    
    /**
     * @Route("/searchAll", name="search_all_post")
     * @Template()
     * @Method("POST")
     */
    public function searchAllPostAction(Request $req) {
        
        $repoGroup = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
        $repoPeople = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        
        $searchedGroups = $repoGroup->searchAllGroups($req->request->get('inserted_text'));
        $searchedPeople = $repoPeople->searchAllPeople($req->request->get('inserted_text'));
        
        if(!$searchedGroups && !$searchedPeople) {
            return [
                'error'=>'Brak wyników'
            ];
        }
        return [
            'searchedGroups'=>$searchedGroups,
            'searchedPeople'=>$searchedPeople
                ];
    }
}
