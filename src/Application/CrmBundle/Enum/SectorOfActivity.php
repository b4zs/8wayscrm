<?php


namespace Application\CrmBundle\Enum;


class SectorOfActivity
{
	public static function getChoices()
	{
		$values = array(
			'Associations / organisations',
			'Automotive market',
			'Aviation / logistics / transport / traffic',
			'Banks',
			'Biotechnology / chemistry / pharmaceutical',
			'Small and Medium business',
			'Catering / hotel business / tourism',
			'Clothing / textile',
			'Communications / marketing / PR / advertising',
			'Construction industry / real estate',
			'Consultancy',
			'E-business / Internet',
			'Education system',
			'Electronics / electrical engineering',
			'Financial services and advice / trust',
			'Forestry / agriculture',
			'Glass / plastic / paper industry',
			'Graphic industry / media / publishing',
			'Health care',
			'Human resources / personnel services',
			'Industry general',
			'Insurance',
			'IT',
			'Law',
			'Leisure / culture / sports',
			'Medical technology',
			'Miscellaneous',
			'Plant / machine / metal construction',
			'Power / water supply',
			'Precision mechanics / optics / watch and clock industry',
			'Public administration',
			'Retail business',
			'Science and research',
			'Semi-luxury food / food',
			'Services general',
			'Telecommunications',
			'Waste management / recycling / environmental technology',
			'Welfare system',
			'Wholesale',
		);

		return array_combine($values, $values);
	}

}