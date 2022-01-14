<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;       
use Doctrine\ORM\EntityRepository;

class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $entityRepository) use ($options){
                    $qb = $entityRepository->createQueryBuilder('u');

                    return $qb
                        ->where("u.PDV =" .$options['id'])
                        ->orderBy('u.nom', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
            'id'=>0
        ]);
    }
}
