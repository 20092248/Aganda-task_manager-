<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agenda;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;


class AgendaController extends Controller
{
    /**
     * @Route("/agenda", name="agenda_list")
     */
    public function listAction(Request $request)
    {
        $listes = $this->getDoctrine()
            ->getRepository('AppBundle:Agenda')
            ->findAll();

        return $this->render('agenda/index.html.twig', array(
            'listes' => $listes
        ));
    }

    /**
     * @Route("/agenda/creer", name="agenda_creer")
     */
    public function createAction(Request $request)
    {
        $agenda = new Agenda;

        $form = $this ->createFormBuilder($agenda)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices' => array('Bas' => 'Bas', 'Moyen' => 'Moyen','Haut' => 'Haut'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Créer un évènement', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Get data
            $name = $form['name']->GetData();
            $category = $form['category']->GetData();
            $description = $form['description']->GetData();
            $priority = $form['priority']->GetData();
            $date = $form['date']->GetData();

            $now = new\DateTime('now');

            $agenda -> setName($name);
            $agenda -> setCategory($category);
            $agenda -> setDescription($description);
            $agenda -> setPriority($priority);
            $agenda -> setDate($date);
            $agenda -> setCreerDate($now);

            $em = $this ->getDoctrine()->getManager();
            $em->persist($agenda);
            $em->flush();

            $this->addFlash(
                'notice',
                'Evènement ajouté'
            );

            return $this->redirectToRoute('agenda_list');
        }

        return $this->render('agenda/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/agenda/modifier/{id}", name="agenda_modifier")
     */
    public function editAction(Request $request, $id)
    {
        $agenda = $this->getDoctrine()
            ->getRepository('AppBundle:Agenda')
            ->find($id);

        $now = new\DateTime('now');

        $agenda -> setName($agenda->getName());
        $agenda -> setCategory($agenda->getCategory());
        $agenda -> setDescription($agenda->getDescription());
        $agenda -> setPriority($agenda->getPriority());
        $agenda -> setDate($agenda->getDate());
        $agenda -> setCreerDate($now);

        $form = $this ->createFormBuilder($agenda)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices' => array('Bas' => 'Bas', 'Moyen' => 'Moyen','Haut' => 'Haut'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Modifier un évènement', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Get data
            $name = $form['name']->GetData();
            $category = $form['category']->GetData();
            $description = $form['description']->GetData();
            $priority = $form['priority']->GetData();
            $date = $form['date']->GetData();

            $now = new\DateTime('now');

            $em = $this ->getDoctrine()->getManager();
            $agenda = $em->getRepository('AppBundle:Agenda')->find($id);

            $agenda -> setName($name);
            $agenda -> setCategory($category);
            $agenda -> setDescription($description);
            $agenda -> setPriority($priority);
            $agenda -> setDate($date);
            $agenda -> setCreerDate($now);


            $em->flush();

            $this->addFlash(
                'notice',
                'Evènement modifié'
            );

            return $this->redirectToRoute('agenda_list');
        }

        return $this->render('agenda/edit.html.twig', array(
            'agenda' => $agenda,
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/agenda/details/{id}", name="agenda_details")
     */
    public function detailsAction($id)
    {
        $agenda = $this->getDoctrine()
            ->getRepository('AppBundle:Agenda')
            ->find($id);

        return $this->render('agenda/details.html.twig', array(
            'agenda' => $agenda
        ));

    }

    /**
     * @Route("/agenda/supprimer/{id}", name="agenda_supprimer")
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $agenda = $em->getRepository('AppBundle:Agenda')->find($id);

        $em->remove($agenda);
        $em->flush();

        $this->addFlash(
            'notice',
            'Tâche suppimer'
        );

        return $this->redirectToRoute('agenda_list');
    }

}
