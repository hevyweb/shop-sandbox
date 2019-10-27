<?php
namespace App\Form;

use App\Entity\Category;
use App\Form\DataTransformer\CategoryTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

class CategoryType extends AbstractType
{
    /**
     * @var CategoryTransformer
     */
    private $categoryTransformer;

    public function __construct(CategoryTransformer $categoryTransformer)
    {
        $this->categoryTransformer = $categoryTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['action'])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
            ->add('parent', HiddenType::class)
        ;

        $builder->get('parent')->addModelTransformer($this->categoryTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Category::class,
        ));
    }
}