<?php

/**
 * Shadow Daemon -- Web Application Firewall
 *
 *   Copyright (C) 2014-2015 Hendrik Buchwald <hb@zecure.org>
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

namespace Swd\AnalyzerBundle\Entity;

use Swd\AnalyzerBundle\Entity\EntityRepositoryTransformer;

/**
 * ParameterRepository
 */
class ParameterRepository extends EntityRepositoryTransformer
{
	public function findAllFiltered(\Swd\AnalyzerBundle\Entity\ParameterFilter $filter)
	{
		$builder = $this->createQueryBuilder('p')
			->leftJoin('p.request', 'r')
			->leftJoin('r.profile', 'v');

		if (!$filter->getIncludeParameterIds()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeParameterIds() as $key => $value)
			{
				$orExpr->add($builder->expr()->eq('p.id', $builder->expr()->literal($value)));
			}

			$builder->andWhere($orExpr);
		}

		if (!$filter->getIncludeProfileIds()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeProfileIds() as $key => $value)
			{
				$orExpr->add($builder->expr()->eq('v.id', $builder->expr()->literal($value)));
			}

			$builder->andWhere($orExpr);
		}

		if (!$filter->getIncludeRequestIds()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeRequestIds() as $key => $value)
			{
				$orExpr->add($builder->expr()->eq('r.id', $builder->expr()->literal($value)));
			}

			$builder->andWhere($orExpr);
		}

		if (!$filter->getIncludeCallers()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeCallers() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('r.caller', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if (!$filter->getIncludeClientIPs()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeClientIPs() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('r.clientIP', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if ($filter->getIncludeDateStart())
		{
			$builder->andWhere('r.date >= :includeDateStart')->setParameter('includeDateStart', $filter->getIncludeDateStart());
		}

		if ($filter->getIncludeDateEnd())
		{
			$builder->andWhere('r.date <= :includeDateEnd')->setParameter('includeDateEnd', $filter->getIncludeDateEnd());
		}

		if (!$filter->getIncludePaths()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludePaths() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('p.path', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if (!$filter->getIncludeValues()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($filter->getIncludeValues() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('p.value', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if ($filter->getIncludeThreat())
		{
			$builder->andWhere('p.threat = :includeThreat')->setParameter('includeThreat', '1');
		}

		if ($filter->getIncludeNoRule())
		{
			$builder->andWhere('p.totalRules = :includeTotalRules')->setParameter('includeTotalRules', '0');
		}

		if ($filter->getIncludeBrokenRule())
		{
			$builder->innerJoin('p.brokenRules', 'b');
		}

		if ($filter->getIncludeCriticalImpact())
		{
			$builder->andWhere('p.criticalImpact = :includeCriticalImpact')->setParameter('includeCriticalImpact', '1');
		}

		if (!$filter->getExcludeParameterIds()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeParameterIds() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->eq('p.id', $builder->expr()->literal($value))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$filter->getExcludeProfileIds()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeProfileIds() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->eq('v.id', $builder->expr()->literal($value))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$filter->getExcludeRequestIds()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeRequestIds() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->eq('r.id', $builder->expr()->literal($value))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$filter->getExcludeCallers()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeCallers() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('r.caller', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}

		if ($filter->getExcludeDateStart())
		{
			$builder->andWhere('r.date < :excludeDateStart')->setParameter('excludeDateStart', $filter->getExcludeDateStart());
		}

		if ($filter->getExcludeDateEnd())
		{
			$builder->andWhere('r.date > :excludeDateEnd')->setParameter('excludeDateEnd', $filter->getExcludeDateEnd());
		}

		if (!$filter->getExcludeClientIPs()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeClientIPs() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('r.clientIP', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$filter->getExcludePaths()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludePaths() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('p.path', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$filter->getExcludeValues()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($filter->getExcludeValues() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('p.value', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}

		if ($filter->getExcludeThreat())
		{
			$builder->andWhere('p.threat != :excludeThreat')->setParameter('excludeThreat', '1');
		}

		if ($filter->getExcludeNoRule())
		{
			$builder->andWhere('p.totalRules != :excludeTotalRules')->setParameter('excludeTotalRules', '0');
		}

		if ($filter->getExcludeBrokenRule())
		{
			// TODO
		}

		if ($filter->getExcludeCriticalImpact())
		{
			$builder->andWhere('p.criticalImpact != :excludeCriticalImpact')->setParameter('excludeCriticalImpact', '1');
		}

		return $builder->getQuery();
	}

	public function findAllLearningBySettings(\Swd\AnalyzerBundle\Entity\GeneratorSettings $settings)
	{
		$builder = $this->createQueryBuilder('p')->leftJoin('p.request', 'r')
			->where('r.mode = 3')
			->andWhere('r.profile = :profile')->setParameter('profile', $settings->getProfile());

		if (!$settings->getIncludeCallers()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($settings->getIncludeCallers() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('r.caller', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if (!$settings->getIncludePaths()->isEmpty())
		{
			$orExpr = $builder->expr()->orX();

			foreach ($settings->getIncludePaths() as $key => $value)
			{
				$orExpr->add($builder->expr()->like('p.path', $builder->expr()->literal($this->prepareWildcard($value))));
			}

			$builder->andWhere($orExpr);
		}

		if (!$settings->getExcludeCallers()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($settings->getExcludeCallers() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('r.caller', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}

		if (!$settings->getExcludePaths()->isEmpty())
		{
			$andExpr = $builder->expr()->andX();

			foreach ($settings->getExcludePaths() as $key => $value)
			{
				$andExpr->add($builder->expr()->not($builder->expr()->like('p.path', $builder->expr()->literal($this->prepareWildcard($value)))));
			}

			$builder->andWhere($andExpr);
		}
			
		return $builder->getQuery();
	}
}
