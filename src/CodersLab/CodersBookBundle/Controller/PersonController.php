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

    private function fileHandle($file, $person, $type) {
        $dir = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/';

        if (!$file)
            return;

        switch ($type) {
            case 'cv':
                $fileName = $person->getCvFN();
                break;
            case 'image':
                $fileName = $person->getImageFN();
                break;
        }

        if (!empty($fileName) && file_exists($dir . $fileName)) {
            unlink($dir . $fileName);
        }
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($dir, $fileName);

        switch ($type) {
            case 'cv':
                $person->setCvFN($fileName);
                break;
            case 'image':
                $person->setImageFN($fileName);
                break;
        }
    }

    private function upload() {


        $form = $this->createFormBuilder()
                ->add('cv', 'file', ['label' => 'Twoje CV'])
                ->add('image', 'file', ['label' => 'Twoje zdjęcie'])
                ->add('save', 'submit', ['label' => 'Wyślij plik'])
                ->getForm();

        return $form;
    }

    private function personForm($person) {


        $form = $this->createFormBuilder($person)
                ->setAction($this->generateUrl('person_admin_create'))
                ->add('name', 'text', ['label' => 'Imię i nazwisko'])
                ->add('email', 'text', ['label' => 'Adres e-mail'])
                ->add('phone', 'text', ['label' => 'Numer telefonu'])
                ->add('github', 'text', ['label' => 'Login Github', 'required' => false])
                ->add('linkedin', 'text', ['label' => 'ID profilu LinkedIn', 'required' => false])
                ->add('clGroup', 'entity', [
                    'label' => 'Grupa',
                    'class' => 'CodersBookBundle:CLGroup',
                    'choice_label' => 'name'])
                ->add('save', 'submit', ['label' => 'Dodaj osobę'])
                ->getForm();
        return $form;
    }

    private function updatePersonForm($person) {

        $form = $this->createFormBuilder($person)
                ->add('name', 'text', ['label' => 'Imię i nazwisko'])
                ->add('email', 'text', ['label' => 'Adres e-mail'])
                ->add('phone', 'text', ['label' => 'Numer telefonu'])
                ->add('github', 'text', ['label' => 'Login Github', 'required' => false])
                ->add('linkedin', 'text', ['label' => 'ID profilu LinkedIn', 'required' => false])
                ->add('clGroup', 'entity', [
                    'label' => 'Grupa',
                    'class' => 'CodersBookBundle:CLGroup',
                    'choice_label' => 'name'])
                ->add('save', 'submit', ['label' => 'Zapisz zmiany'])
                ->getForm();
        return $form;
    }

    /**
     * @Route("/all/{name}", name = "person_admin_all")
     * @Template()
     */
    public function showAllPersonsAction($name) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $repoGroup = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');

        $group = $repoGroup->findOneByName($name);
        if (!$group) {
            return [
                'error' => 'Nie ma takiej grupy'
            ];
        }
        $persons = $repo->findBy(['clGroup' => $group]);

        return [
            'persons' => $persons,
            'clGroup' => $group
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

    /**
     * @Route("/admin/update/{id}", name = "person_admin_update")
     * @Method("GET")
     * @Template()
     */
    public function updatePersonGetAction($id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');

        $person = $repo->find($id);
        if (!$person) {
            return [
                'error' => 'Wystąpił błąd brak takiej osoby w bazie danych!'
            ];
        }
        $form = $this->updatePersonForm($person);
        return[
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/update/{id}", name = "person_admin_save")
     * @Method("POST")
     * @Template("CodersBookBundle:Person:updatePersonGet.html.twig")
     */
    public function updatePersonPostAction(Request $req, $id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $person = $repo->find($id);
        $form = $this->updatePersonForm($person, $person->getId());
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
        }
        return [
            'form' => $form->createView(),
            'success' => true
        ];
    }

    /**
     * @Route("/admin/upload/{id}", name = "person_admin_upload")
     * @Template()
     */
    public function uploadPersonAction(Request $req, $id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $person = $repo->find($id);

        $form = $this->upload();
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $cv = $form->get('cv')->getData();
            $image = $form->get('image')->getData();

            $this->fileHandle($cv, $person, 'cv');
            $this->fileHandle($image, $person, 'image');

            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }


        return [
            'form' => $form->createView()
        ];
    }

}
