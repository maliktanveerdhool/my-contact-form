<?php return array(
    'root' => array(
        'name' => 'youruser/my-contact-form',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'yahnis-elsts/plugin-update-checker' => array(
            'pretty_version' => 'v5.6',
            'version' => '5.6.0.0',
            'reference' => 'a2db6871deec989a74e1f90fafc6d58ae526a879',
            'type' => 'library',
            'install_path' => __DIR__ . '/../yahnis-elsts/plugin-update-checker',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'youruser/my-contact-form' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
