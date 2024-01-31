<?php

namespace App\Form;

use App\Entity\Book;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;   
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Validator\Constraints\File;

use App\Entity\Author;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isbn')
            ->add('title')
            ->add('cover', FileType::class,[
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
            ->add('authors', EntityType::class, array(
                'class' => Author::class,
                'choice_label' => 'name',
                "label" => "Author: ",
                'multiple' => true))

            ->add('save', SubmitType::class, array('label' => 'Add Book'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
