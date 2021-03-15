<?php

namespace Drupal\wmusersnap\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wmusersnap\UsersnapInterface;

class SettingsForm extends FormBase
{
    public function getFormId(): string
    {
        return 'wmusersnap_settings';
    }

    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $config = $this->config('wmusersnap.settings');

        $form['api_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('API key'),
            '#default_value' => $config->get('api_key'),
        ];

        $form['enable'] = [
            '#type' => 'select',
            '#title' => $this->t('Enable the integration'),
            '#options' => [
                UsersnapInterface::STATUS_ENABLED_IF_PERMISSION => $this->t('If the user has permission'),
                UsersnapInterface::STATUS_ENABLED => $this->t('Always'),
                UsersnapInterface::STATUS_DISABLED => $this->t('Never'),
            ],
            '#default_value' => $config->get('enable') ?? UsersnapInterface::STATUS_DISABLED,
            '#required' => true,
        ];

        $form['cookie_domain'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Cookie domain'),
            '#description' => $this->t('The domain on which the cookie will be set. If this site exists on 
                multiple subdomains of the same parent domain, set this to the parent domain. If this is left empty, 
                the current domain is used.'),
            '#default_value' => $config->get('cookie_domain'),
            '#states' => [
                'visible' => [':input[name="enable"]' => ['!value' => UsersnapInterface::STATUS_DISABLED]],
            ],
        ];

        $form['domains'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Domains'),
            '#description' => $this->t('A list of domains on which to enable the integration. This is most 
                likely the current domain or subdomains of the current domain.'),
            '#default_value' => implode(PHP_EOL, $config->get('domains') ?? []),
            '#states' => [
                'visible' => [':input[name="enable"]' => ['!value' => UsersnapInterface::STATUS_DISABLED]],
            ],
        ];

        $form['remove_cookie_on_logout'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Remove cookie after logging out'),
            '#description' => $this->t('When enabled, the user loses access to the widget immediately after 
                logging out. Otherwise, the cookie will expire after three weeks.'),
            '#default_value' => $config->get('remove_cookie_on_logout'),
            '#states' => [
                'visible' => [':input[name="enable"]' => ['!value' => UsersnapInterface::STATUS_DISABLED]],
            ],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save'),
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $config = $this->configFactory->getEditable('wmusersnap.settings');

        $config->set('api_key', $form_state->getValue('api_key'));
        $config->set('enable', $form_state->getValue('enable'));
        $config->set('cookie_domain', $form_state->getValue('cookie_domain'));
        $config->set('remove_cookie_on_logout', $form_state->getValue('remove_cookie_on_logout'));

        $domains = explode(PHP_EOL, $form_state->getValue('domains'));
        $domains = array_map('trim', $domains);
        $config->set('domains', $domains);

        $config->save();

        $this->messenger()->addStatus('Settings successfully updated');
    }
}
