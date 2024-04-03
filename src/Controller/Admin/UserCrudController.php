<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstname'),
            TextField::new('lasttname'),
            TextField::new('adresse'),
            TextField::new('city'),
            TextField::new('zipcode'),
            TextField::new('telephone'),
            TextField::new('email'),
            BooleanField::new('isVerified'),
            ChoiceField::new('roles')->setChoices([
                'USER' => 'ROLE_USER',
                'ADMIN' => 'ROLE_ADMIN',
            ])
        ];
    }
    
}
