<?php

namespace CodersLab\CodersBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use CodersLab\CodersBookBundle\Entity\CLGroup;
use CodersLab\CodersBookBundle\Entity\Person;

/**
 * @Route("/group")
 */
class CLGroupController extends Controller {

    /**
     * @Route("/admin/all", name="group_admin_all")
     * @Template()
     */
    public function showAllAction() {

        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
        $groups = $repo->findAll();

        return [
            'groups' => $groups
        ];
    }

    public function groupForm($group) {
        $form = $this->createFormBuilder($group)
                ->setAction($this->generateUrl('group_admin_create'))
                ->add('name', 'text', ['label' => 'Nazwa grupy'])
                ->add('lecturer', 'text', ['label' => 'Wykładowca'])
                ->add('save', 'submit', ['label' => 'Dodaj grupę'])
                ->getForm();
        return $form;
    }

    /**
     * @Route("/admin/new", name="group_admin_new")
     * @Template()
     */
    public function newGroupAction() {
        $group = new CLGroup();

        $form = $this->groupForm($group);

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/create", name="group_admin_create")
     * @Template()
     */
    public function createGroupAction(Request $req) {
        $group = new CLGroup();
        $form = $this->groupForm($group);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $repo = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
            $groupWithName = $repo->findOneByName($group->getName());

            if ($groupWithName) {
                return [
                    'error' => 'Nazwa grupy już istnieje'
                ];
            }

            if ($group->getName() == '') {
                return [
                    'error' => 'Wpisz poprawną nazwę grupy'
                ];
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            return [];
        }
    }

    /**
     * @Route("/admin/delete/{id}")
     * @Template()
     * @Method("GET")
     */
    public function deleteGroupAction($id) {
        $repo = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
        $groups = $repo->findAll();

        return [
            'groups' => $groups
        ];
    }

    /**
     * @Route("/admin/delete/{id}")
     * @Template()
     * @Method("POST")
     */
    public function delete2GroupAction(Request $req, $id) {

        $selectedGroupId = $req->request->get('selectedGroup');

        if ($id == $selectedGroupId) {
            return [
                'error' => 'Wybierz inną grupę'
            ];
        }

        $repoClGroup = $this->getDoctrine()->getRepository('CodersBookBundle:CLGroup');
        $clGroupOld = $repo->find($id);
        $clGroupNew = $repo->find($selectedGroupId);

        $repoPerson = $this->getDoctrine()->getRepository('CodersBookBundle:Person');
        $em = $this->getDoctrine()->getManager();

        if ($clGroupOld && $clGroupNew) {
            $persons = $repo->findByClGroup($clGroupOld);

            foreach ($persons as $person) {
                $person->setClGroup($clGroupNew);
            }
            
            $em->remove($clGroupOld);
            $em->flush();
        }
        
        return[];
    }

}
