<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TacheController extends AbstractController
{
    /**
     * @Route("/", name="tache")
     */
    public function index(Request $request)
    {
        $sortParam = $request->query->get('sort');
        if (isset($sortParam) && in_array($sortParam, ['created', 'titre', 'status'])) {
            $sortBy = 't.' . $sortParam;
            $entityManager = $this->getDoctrine()->getManager();
            $qb = $entityManager->createQueryBuilder();
            $qb->select('t')
               ->from('App\Entity\Tache', 't')
               ->orderBy($sortBy);
            $query = $qb->getQuery();
            $Listetaches = $query->execute();
        } else {
            $repository = $this->getDoctrine()->getRepository(Tache::class);
            $Listetaches = $repository->findAll();         
        }
        return $this->render('index.html.twig', array('Listetaches' => $Listetaches));
    }
    /**
     * @Route("/new", name="new")
     */

    public function new(){

        $obj = new Tache();
        $obj->setTitre('Nouvelles taches');
        $form = $this->createFormBuilder($obj)
        ->add('titre', TextType::class)
        ->add('description', TextareaType::class)
        ->add('status', ChoiceType::class, array(
            'choices'  => array(
                'A faire' => 0,
                'En cours' => 1,
                'Terminée' => 2,
            ),
        ))
        ->add('save', SubmitType::class, array('label' => 'Créer une tâche'))
        ->getForm();

        if (isset($_POST['form'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $tache = new Tache();
            $tache->setTitre($_POST['form']['titre']);
            $tache->setDescription($_POST['form']['description']);
            $tache->setStatus($_POST['form']['status']);
            $tache->setCreated(new \DateTime());
            $entityManager->persist($tache);
            $entityManager->flush();
            return $this->redirectToRoute('tache');
        }

        return $this->render('new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

       /**
     * @Route("/update/{id}")
     */

       public function update($id){

        $entityManager = $this->getDoctrine()->getManager();
        $taches = $entityManager->getRepository(Tache::class)->find($id);
        $obj = new Tache();
        $obj->setTitre($taches->getTitre());
        $obj->setDescription($taches->getDescription());
        $form = $this->createFormBuilder($obj)
        ->add('titre', TextType::class)
        ->add('description', TextareaType::class)
        ->add('status', ChoiceType::class, array(
            'choices'  => array(
                'A faire' => 0,
                'En cours' => 1,
                'Terminée' => 2,
            ),
        ))
        ->add('save', SubmitType::class, array('label' => 'Modifier une tâche'))
        ->getForm();

        if (isset($_POST['form'])) {
           $taches->setTitre($_POST['form']['titre']);
           $taches->setDescription($_POST['form']['description']);
           $taches->setStatus($_POST['form']['status']);
           $entityManager->flush();
            return $this->redirectToRoute('tache');
       }

       return $this->render('update.html.twig', array(
        'form' => $form->createView(),

    ));
   }

      /**
     * @Route("/delete/{id}")
     */

      public function delete($id){

        $entityManager = $this->getDoctrine()->getManager();
        $taches = $entityManager->getRepository(Tache::class)->find($id);
        $entityManager->remove($taches);
        $entityManager->flush();
        $repository = $this->getDoctrine()->getRepository(Tache::class);
        $question = $repository->findAll();
        return $this->redirectToRoute('tache');
    }


}
