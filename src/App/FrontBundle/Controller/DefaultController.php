<?php

namespace App\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use App\FrontBundle\Entity\Livre;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();
        $categories = $em->getRepository('AppFrontBundle:Category')->findAll();
        $livres = $em->getRepository('AppFrontBundle:Livre')->findAll();
        
        return $this->render('AppFrontBundle:Default:index.html.twig', array(
            'users' => $users,
            'categories' => $categories,
            'livres' => $livres,
        ));
    }

    public function usersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();
        
        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    public function edituserAction(User $user)
    {
        if ($user->isEnabled()) {
        	$user->setEnabled(0);
        }else{
        	$user->setEnabled(1);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $users = $em->getRepository('AppBundle:User')->findAll();
        
        return $this->redirectToRoute('app_front_users');
    }

     public function empruntAction(Livre $livre)
    {
        $user1=$this->getUser();
        $users=$livre->getUsers();
        foreach ($users as $user) {
            if ($user==$user1) {
                $livre->removeUser($user1);
                $livre->setNbexemplaire($livre->getNbexemplaire()+1);
                $em = $this->getDoctrine()->getManager();
                $em->persist($livre);
                $em->flush();
                return $this->redirectToRoute('livre_index');
            }
        }

        $livre->addUser($user1);
        $livre->setNbexemplaire($livre->getNbexemplaire()-1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($livre);
        $em->flush();
        return $this->redirectToRoute('livre_index');
    } 

    public function meslivresAction()
    {
        $em = $this->getDoctrine()->getManager();

        $current=$this->getUser();
        $livres = $em->getRepository('AppFrontBundle:Livre')->findAll();
        $mybooks = array();

        foreach ($livres as $livre) {
            
            foreach ($livre->getUsers() as $user) {
                if ($user==$current) {
                   array_push($mybooks, $livre); 
                }
            }
        }

        return $this->render('livre/mes-emprunts.html.twig', array(
            'mybooks' => $mybooks,
        ));
    }  

    public function accessdeniedAction()
    {
       
        return $this->render('AppFrontBundle:Default:403.html.twig');
    }          
}