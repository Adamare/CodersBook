<?php

namespace CodersLab\CodersBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use CodersLabBundle\Entity\CLGroup;
use CodersLabBundle\Entity\Person;

class SearchController extends Controller {
    
    /**
     * @Route("/searchAll", name="search_all_get")
     * @Template()
     * @Method("GET")
     */
    public function searchAllGetAction() {
        return [];
    }
    
    /**
     * @Route("/searchAll", name="search_all_post")
     * @Template()
     * @Method("POST")
     */
    public function searchAllPostAction(Request $req) {
        
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
        $em = $this->getDoctrine()->getManager();
        $searchingText = $req->request->get('inserted_text');
        
        $query = $em->createQuery('SELECT clgroup, person FROM CodersBookBundle:CLGroup clgroup, CodersBookBundle:Person  person'
                . ' WHERE clgroup.name LIKE :text OR clgroup.lecturer LIKE :text '
                . ' OR person.name LIKE :text'
                . ' OR person.email LIKE :text'
                . ' OR person.phone LIKE :text'
                . ' OR person.github LIKE :text'
                . ' OR person.linkedin LIKE :text')->setParameter('text', '%' . $searchingText .'%');
        
        $results = $query->getResult();
        
        if(!$results) {
            return [
                'error'=>'Brak wyników'
            ];
        }
        return [
            'results'=>$results
                ];
    }
}
