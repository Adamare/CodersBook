<?php

namespace CodersLab\CodersBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use CodersLab\CodersBookBundle\Entity\Person;
use CodersLab\CodersBookBundle\Entity\CLGroup;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends Controller {
    /**
     * @Route("/showAllPersons", name = "showAllPersons")
     * @Template()
     */
    public function showAllPersonsAction() {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');

        $persons = $repo->findAll();
        return [
            'persons' => $persons,
            
        ];
    }
}
