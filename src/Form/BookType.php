<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Название книги'])
            ->add('author', null, ['label' => 'Автор'])
            ->add('date_read', DateType::class, ['label' => 'Дата прочтения'])
            ->add('cover', FileType::class, [
                'label' => 'Изображение обложки книги (jpg, png)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Доступные форматы для загрузки: jpg, png',
                    ])
                ],
            ])
            ->add('file', FileType::class, [
                'label' => 'Файл книги (epub, txt)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/epub+zip',
                            'text/plain',
//                            'application/x-fictionbook', 'application/x-fictionbook+xml',
                        ],
                        'mimeTypesMessage' => 'Доступные форматы для загрузки: epub, txt',
                    ])
                ],
            ])
            ->add('is_download',  CheckboxType::class, [
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
