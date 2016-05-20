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

/**
 * @Route("/person")
 */
class PersonController extends Controller {

    private function personForm($person) {

        $form = $this->createFormBuilder($person)
                ->setAction($this->generateUrl('person_admin_create'))
                ->add('name', 'text', ['label' => 'Imię i nazwisko'])
                ->add('email', 'text', ['label' => 'Adres e-mail'])
                ->add('phone', 'text', ['label' => 'Numer telefonu'])
                ->add('github', 'text', ['label' => 'Login Github'])
                ->add('linkedin', 'text', ['label' => 'ID profilu LinkedIn'])
                ->add('clGroup', 'entity', [
                    'label' => 'Grupa',
                    'class' => 'CodersBookBundle:CLGroup',
                    'choice_label' => 'name'])
                ->add('save', 'submit', ['label' => 'Dodaj osobę'])
                ->getForm();
        return $form;
    }

    /**
     * @Route("/admin/all/{id}", name = "person_admin_all")
     * @Template()
     */
    public function showAllPersonsAction($id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $repoGroup = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');

        $persons = $repo->findBy(['clGroup' => $repoGroup->find($id)]);
        return [
            'persons' => $persons,
        ];
    }

    /**
     * @Route("/admin/create", name = "person_admin_create")
     * @Template()
     */
    public function createPersonAction(Request $req) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $person = new Person();

        $form = $this->personForm($person);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            if ($repo->findByName($person->getName()) || $person->getName() == '') {
                return [
                    'error' => 'Taka osoba już istnieje lub formularz jest pusty!'
                ];
            }
            if (!$person->getClGroup()) {
                return [
                    'error' => 'Nie wybrano grupy!'
                ];
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return [
                'person' => $person
            ];
        }
    }
    /**
     * @Route("/admin/new", name = "person_admin_new")
     * @Template()
     */
    public function newPersonAction() {
        $person = new Person();

        $form = $this->personForm($person);

        return [
            'form' => $form->createView()
        ];
    }
    
    /**
     * @Route("/admin/delete/{id}", name = "person_admin_delete")
     * @Template()
     */
    public function deletePersonAction($id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $em = $this->getDoctrine()->getManager();
        $deletedPerson = $repo->find($id);

        if ($deletedPerson) {
            $em->remove($deletedPerson);
            $em->flush();
        }

        return [
            'deletedPerson' => $deletedPerson
        ];
    }

}
