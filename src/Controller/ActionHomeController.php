<?php

namespace App\Controller;

use App\Repository\TypeNameRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\TypeName;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AjouterActionType;
use App\Form\Type;
use App\Entity\Action;
use App\Repository\ActionRepository;
use App\Repository\ActiontypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
class ActionHomeController extends AbstractController
{
    #[Route('/home/{id}', name: 'app_home')]
    public function index(UserRepository $rep, $id,ActionRepository $acterep): Response
    {   
        $users= $rep->findById($id);
        $user = $rep->findOneBy(['id' => $id]);
        
        if (!$users) {
            throw $this->createNotFoundException('User not found');
        }
        // last action block 
        $lastAction = null;
        $actions = $acterep->findBy(['user' => $user], ['date' => 'DESC']);
       
        $lastActionDate = null;
        foreach ($actions as $action) {
            if ($lastActionDate === null || $action->getDate() < $lastActionDate) {
                $lastActionDate = $action->getDate()->format('Y-m-d H:i:s');
            }
        }
        $labels = [];
        $data = [];
    //show score par date 
    $scoreByDate = [];
    foreach ($actions as $action) {
        $date = $action->getDate()->format('Y-m-d');
        $score = $action->getActionScore();
        if (!isset($scoreByDate[$date])) {
            $scoreByDate[$date] = 0;
        }
        $scoreByDate[$date] += $score;
    }
        //chart block
        $labels = array_keys($scoreByDate);
        $data = array_values($scoreByDate); 
        return $this->render('action_home/HomeAction.html.twig', [
            'user' => $user,
            'labels' => json_encode($labels),
            'data' => json_encode($data),
            'lastActionDate' => $lastActionDate,
        ]);
    }   
    #[Route('/action/suivre/{id}', name: 'app_action_suivre')]
    public function showquotidien(ActionRepository $rep,ActiontypeRepository $rep1,$id,UserRepository $repUser): Response
    {
        $actions = $rep->findBy(['user' => $id]);
        $user=$repUser->findOneBy(['id' => $id]);
        //chart
        $labels = [];
        $data = [];
        $labels2 = [];
        $data2=[]; 
        foreach ($actions as $action) { 
            $type = $action->getType()->getNom();
            $score = $action->getActionScore();

            if (!isset($sums[$type])) {
                $sums[$type] = 0;
            }

            $sums[$type] += $score;
            $date = $action->getDate(); 
            $formattedDate = $date->format('Y-m-d'); 
            $labels[] = $formattedDate;
            $data[] = $action->getActionScore();  
        }
        $data2 = array_values($sums);
        $labels2 = array_keys($sums);
            return $this->render('action_home/QuatidienAction.html.twig', ['actions'=>$actions,'labels' => json_encode($labels),
            'data' => json_encode($data),'labels2' => json_encode($labels2),
            'data2' => json_encode($data2),'user'=>$user]);
    }
    
    #[Route('/action/ajouter/{id}', name: 'app_action_ajouter')]
   public function ajouteraction(ManagerRegistry $doctrine, Request $request,$id,UserRepository $repUser,TypeNameRepository $typerep,ActiontypeRepository $actiontypes,ActionRepository $actrep): Response
{
    $action = new Action();
    $types=$typerep->findAll();
    $actiontypes=$actrep->findAll();
    foreach ( $types as $type)
    {
        if( $type->getActiontype()->getNom() =="Consommation d'énergie"){
            $energy[]=$type;
        }
        elseif( $type->getActiontype()->getNom() =="Consommation matérielle"){
            $materielle[]=$type;
        }   
    }
    

    $user=$repUser->find($id);
    $action->setDate(new \DateTime());
    $form = $this->createForm(AjouterActionType::class, $action);
    $form->handleRequest($request);
    // last action block 
    $lastAction = null;
    $actions = $actrep->findBy(['user' => $user], ['date' => 'DESC']);
    $ac = $actrep->findBy(['user' => $user]);
    $lastActionDate = null;
    foreach ($actions as $action) {
        if ($lastActionDate === null || $action->getDate() < $lastActionDate) {
            $lastActionDate = $action->getDate()->format('Y-m-d H:i:s');
        }
    }
    if ($form->isSubmitted() && $form->isValid()) {
        //$selectedUser = $form->get('user')->getData();
        $tp = $form->get('type')->getData();
        if (null == $tp) {
            throw new \RuntimeException('Type is null, expected an instance of ActionType');
        }
        //$type = $typerep->find(['id' => $tp]);
      
        $quantite = intval($action->getQuantite());
            $scoretype = $type->getScore();
        if($quantite !== null){
         
        
        if ($id !== null) {
            $action->setUser($user);
            $action->setType($type);
            $em = $doctrine->getManager();
            // calcul the action score
            $score = $scoretype * $quantite;
            $action->setActionScore($score);           
            }
            $em->persist($action);
            $em->flush();
            return $this->redirectToRoute('app_action_home', ['id' => $id]);
        }
        else{
            // condition el quantite time
            $quantite = $action->getQuantiteTime();
            $scoretype = $type->getScore(); 
            
            $em = $doctrine->getManager();
            
            if ($id !== null) {
                $action->setUser($user);
                
                // calcul the action score
                $score = $scoretype;
                $action->setActionScore($score);           
                }
                $em->persist($action);
                $em->flush();
                return $this->redirectToRoute('app_action_home', ['id' => $id]);
            }
    }
    return $this->render('action_home/addactionform.html.twig', [
        'formA' => $form->createView(),'user'=>$user,'actions'=>$ac,'actiontypes'=>$actiontypes,'lastActionDate' => $lastActionDate,
    ]);
}
#[Route('/action/update-form/{typeId}/{userId}', name: 'app_action_update_form_type')]
public function updateForm(Request $request,$userId,$typeId, TypeNameRepository $typerep,ActionTypeRepository $actionttypes, UserRepository $userrep,ActionRepository $actrep): Response
{
    $type = $typerep->findOneBy(['actiontype' => $typeId]);
    $user = $userrep->find($userId);
    $actions = $actionttypes->findAll();
    $form = $this->createForm(AjouterActionType::class);
 //  foreach ($type as $t) {
    $form->get('type')->clearErrors();
        $form->get('type')->setData($type);
  //  }
    // last action block 
    $lastAction = null;
    $actions = $actrep->findBy(['user' => $user], ['date' => 'DESC']);
    $ac = $actrep->findBy(['user' => $user]);
    $lastActionDate = null;
    foreach ($actions as $action) {
        if ($lastActionDate === null || $action->getDate() < $lastActionDate) {
            $lastActionDate = $action->getDate()->format('Y-m-d H:i:s');
        }
    }
    if ($form->get('type')->getData() === null) {
        throw new \RuntimeException('Type is null, expected an instance of ActionType');
    }
    $typeFieldHtml = $this->renderView('action_home/addactionform.html.twig', [
        'formA' => $form->createView(),
        'actions' => $actions,
        'actiontypes' => $actions,
        'user' => $user,
        'lastActionDate' => $lastActionDate,
    ]);
    return new Response($typeFieldHtml);
}

#[Route('/action/modifier/{id}/{idaction}', name: 'app_action_modifier')]
     public function modifieraction(ManagerRegistry $doctrine, Request $request, ActionRepository $rep, $id,$idaction,UserRepository $userrep): Response
     {
        $user = $userrep->findOneBy(['id' => $id]);
        $action = $rep->find($idaction);
        $actions=$rep->findAll();
        // last action block 
        $lastAction = null;
        $actions = $rep->findBy(['user' => $user], ['date' => 'DESC']);
        $lastActionDate = null;
        foreach ($actions as $action) {
            if ($lastActionDate === null || $action->getDate() < $lastActionDate) {
                $lastActionDate = $action->getDate()->format('Y-m-d H:i:s');
            }
        }
        $form=$this->createForm(AjouterActionType::class,$action);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->persist($action);
            $em->flush();
            return $this-> redirectToRoute('app_action_ajouter', ['id' => $id]);
        }
        return $this->render('action_home/modifieractionform.html.twig',[
            'formA' => $form->createView(),
            'action'=> $action,
            'actions'=> $actions,
            'user'=>$user,
            'lastActionDate'=>$lastActionDate
        ]);
     }
#[Route('/action/delete/{id}/{idaction}', name: 'app_action_supprimer')]
     public function deleteAuthor($id,$idaction, ActionRepository $actrep, ManagerRegistry $doctrine,UserRepository $userrep): Response
     {
         $em= $doctrine->getManager();
         $user = $userrep->findOneBy(['id' => $id]);
         $action = $actrep->find($idaction);
         $actions=$actrep->findAll();
         $lastActionDate = null;
        foreach ($actions as $action) {
            if ($lastActionDate === null || $action->getDate() < $lastActionDate) {
                $lastActionDate = $action->getDate()->format('Y-m-d H:i:s');
            }
        }
         $em->remove($action);
         $em->flush();
         return $this-> redirectToRoute('action_home/ajouteractionform.html.twig',[
            'action'=> $action,
            'actions'=> $actions,
            'user'=>$user,
            'lastActionDate'=>$lastActionDate
        ]);
     }

////////////////////////////////////  ADMIN /////////////////////////////////////////////


//////// type controller ////////////
#[Route('/admin/{id}', name: 'admin')]
public function admin(ActionRepository $rep,$id,UserRepository $repUser,TypeNameRepository $typerep): Response
{
        $user=$repUser->findOneBy(['id' => $id]);
        $actions=$rep->findAll();
        $types=$typerep->findAll();
        return $this->render('action_back/admin_interface.html.twig',[
            'actions' => $actions,
            'types' => $types,
            'user' => $user
        ]); 
}
#[Route('/admin/typename/ajouter/{id}', name: 'admin-ajouter-typename')]
public function ajoutertypename(ManagerRegistry $doctrine, Request $request,$id,UserRepository $repUser): Response
{
    $user=$repUser->findOneBy(['id' => $id]);
    $type =new TypeName();
    $form=$this->createForm(Type::class,$type);
    $form->handleRequest($request);
    if($form->isSubmitted()){
        $em= $doctrine->getManager();
        $em->persist($type);
        $em->flush();
        return $this-> redirectToRoute('admin', ['id' => $id]);
    }
 return $this->render('action_back/admin_ajouter_typename.html.twig', [
     'formA' => $form->createView(),
     'user' => $user
 ]);
}
#[Route('/admin/update/{id}', name: 'admin-modifier-typename')]
     public function modifiertypename(ManagerRegistry $doctrine, Request $request, TypeNameRepository $rep, $id,UserRepository $repUser): Response
     {
        $user=$repUser->findOneBy(['id' => $id]);
        $type = $rep->find($id);
        $form=$this->createForm(Type::class,$type);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->persist($type);
            $em->flush();
            return $this-> redirectToRoute('admin', ['id' => $id]);
        }
        return $this->render('action_back/admin_modifier_typename.html.twig',[
            'formA'=>$form->createView(),
            'user' => $user
        ]);
     }
     
#[Route('/admin/typename/delete/{iduser}/{idtype}', name: 'admin-supprimer-typename')]
     public function supprimertypename($iduser,$idtype, TypeNameRepository $rep, ManagerRegistry $doctrine,UserRepository $repUser): Response
     {
         $user=$repUser->findOneBy(['id' => $iduser]);
         $em= $doctrine->getManager();
         $type= $rep->find($idtype);
         if ($type !== null) {
            $em->remove($type);
            $em->flush();
         }
         return $this-> redirectToRoute('admin', ['id' => $iduser]);
     }
//////////////////// action controller //////////////////
     #[Route('/admin/update/{id}', name: 'admin-modifier-action')]
     public function adminmodifieraction(ManagerRegistry $doctrine, Request $request, ActionRepository $rep, $id,UserRepository $repUser): Response
     {
        $user=$repUser->findOneBy(['id' => $id]);
        $action = $rep->find($id);
        $form=$this->createForm(AjouterActionType::class,$action);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em= $doctrine->getManager();
            $em->persist($action);
            $em->flush();
            return $this-> redirectToRoute('admin', ['id' => $id]);
        }
        return $this->render('action_back/admin_modifier_typename.html.twig',[
            'formA'=>$form->createView(),
            'user' => $user
        ]);
     }
     #[Route('/admin/action/delete/{iduser}/{idaction}', name: 'admin-supprimer-action')]
     public function supprimeraction($iduser,$idaction, ActionRepository $rep, ManagerRegistry $doctrine,UserRepository $repUser): Response
     {
         $user=$repUser->findOneBy(['id' => $iduser]);
         $em= $doctrine->getManager();
         $action= $rep->find($idaction);
         if ($action !== null) {
            $em->remove($action);
            $em->flush();
         }
         return $this-> redirectToRoute('admin', ['id' => $iduser]);
     }
}

