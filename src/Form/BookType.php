<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Название книги'])
            ->add('author', null, ['label' => 'Автор'])
            ->add('cover', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Изображение обложки книги',
                'required' => false,
            ])
            ->add('file', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Файл книги',
                'required' => false,
            ])
            ->add('date_read', \Symfony\Component\Form\Extension\Core\Type\DateType::class, ['label' => 'Дата прочтения'])
            ->add('is_download',  \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'label' => 'Разрешено скачивать',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
