<?php

namespace CodersLab\CodersBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller {

    /**
     * @Route("/new/admin/user")
     * @Method("GET")
     * @Template()
     */
    public function newAdminAction() {

        return [];
    }

    /**
     * @Route("/new/admin/user")
     * @Method ("POST")
     * @Template("CodersBookBundle:Default:newAdmin.html.twig")
     */
    public function createAdminAction(Request $req) {
        $code = $this->getParameter('admin_create_access_code');
        $userCode = $req->request->get('code');


        if ($userCode != $code) {
            return new Response('Access denied!');
         
        }
        

        $username = $req->request->get('login');
        $password = $req->request->get('pass');
        $email = $req->request->get('mail');

        $userManager = $this->get('fos_user.user_manager');
        $admin = $userManager->createUser();

        $admin->setUsername($username);
        $admin->setEmail($email);
        $admin->setPassword($password);
        $admin->setEnabled(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($admin);
        $em->flush();


        return [
            'admin' => $admin
        ];
    }

}
