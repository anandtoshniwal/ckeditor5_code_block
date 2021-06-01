<?php

namespace Drupal\ckeditor5_code_block\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginContextualValidationInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;

/**
 * CKEditor5 Language plugin.
 */
class CodeBlock extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface, CKEditor5PluginContextualValidationInterface {

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, Editor $editor) {
    $language_list = [];
    $plugin_config = [
      ['language' => 'html', 'label' => 'HTML'],
      ['language' => 'css', 'label' => 'CSS'],
    ];
    $settings = $editor->getSettings();

    if (isset($settings['plugins']['codeBlock']) && isset($settings['plugins']['codeBlock']['code_block_languages'])) {
      $plugin_config = $settings['plugins']['codeBlock']['code_block_languages'];
    }

    $languages = explode(PHP_EOL, $plugin_config);
    foreach ($languages as $language) {
      if (!empty($language)) {
        $lang = explode('|', $language);
        $language_list[] = ['language' => trim($lang[0]), 'label' => trim($lang[1])];
      }
    }

    $config = $static_plugin_config;
    $config['codeBlock']['languages'] = $language_list;
    return $config;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\editor\Form\EditorImageDialog
   * @see editor_image_upload_settings_form()
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    // Defaults.
    $config = [
      ['language' => 'html', 'label' => 'HTML'],
      ['language' => 'css', 'label' => 'CSS'],
    ];

    $settings = $editor->getSettings();
    if (isset($settings['plugins']['codeBlock']) && isset($settings['plugins']['codeBlock']['code_block_languages'])) {
      $config = $settings['plugins']['codeBlock']['code_block_languages'];
    }

    $form['code_block_languages'] = [
      '#title' => 'Enter lanugages',
      '#type' => 'textarea',
      '#required' => TRUE,
      '#description' => 'Enter each language object on the new line.',
      '#default_value' => $config,
    ];

    return $form;
  }

  /**
   * Allow this plugin to validate the editor settings form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Editor settings form state.
   */
  public function validateFilterForm(FormStateInterface $form_state) {
    $code_block_settings = explode(PHP_EOL, $form_state->getValue('editor')['settings']['plugins']['codeBlock']['code_block_languages']);

    foreach ($code_block_settings as $language) {
      if (!empty($language)) {
        $lang = explode('|', $language);
        kint(strlen(trim($lang[1])));
        if (!strlen(trim($lang[0])) || !strlen(trim($lang[1]))) {
          $form_state->setErrorByName('code_block_languages', $this->t('Plesae enter languages in language|label format.'));
        }
      }
    }

  }

}
