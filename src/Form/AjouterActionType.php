<?php

namespace App\Form;
use App\Entity\ActionType;
use App\Entity\Action;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\ActiontypeRepository;
use App\Repository\TypeNameRepository;
class AjouterActionType extends AbstractType
{   
    private $actionTypeRepository;
    private $typeRepository;

    public function __construct(ActionTypeRepository $actionTypeRepository, TypeNameRepository $typeRepository)
    {
        $this->actionTypeRepository = $actionTypeRepository;
        $this->typeRepository = $typeRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('quantite_time',TimeType::class)
            ->add('date')
            ->add('description')
            ->add('type')
            ->add('categorie')
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
        ]);
    }
}
