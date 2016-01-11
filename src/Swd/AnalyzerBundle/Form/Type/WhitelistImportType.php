<?php

/**
 * Shadow Daemon -- Web Application Firewall
 *
 *   Copyright (C) 2014-2016 Hendrik Buchwald <hb@zecure.org>
 *
 * This file is part of Shadow Daemon. Shadow Daemon is free software: you can
 * redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Swd\AnalyzerBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WhitelistImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile', 'entity', array('property' => 'getIdAndName', 'class' => 'SwdAnalyzerBundle:Profile',
                'query_builder' => function(EntityRepository $er) { return $er->createQueryBuilder('v')->orderBy('v.id', 'ASC'); }
            ))
            ->add('base', null, array('required' => false))
            ->add('file', 'file')
            ->add('actions', 'form_actions', array('buttons' => array('import' => array('type' => 'submit'), 'reset' => array('type' => 'reset'))));
    }

    public function getName()
    {
        return 'whitelist_import';
    }
}
