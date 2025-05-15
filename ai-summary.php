<?php

/**
 * Plugin Name:       AI Post Summary
 * Plugin URI:        https://github.com/yourname/ai-summary
 * Description:       Generates and caches a short summary of an article via OpenAI and outputs it via the shortcode [ai_summary].
 * Version:           1.0.0
 * Author:            Denys Astapov
 * Author URI:        https://github.com/denysastapov
 * Text Domain:       ai-summary
 */

// Если файл зашёл не через WP — выходим
defined('ABSPATH') || exit;

// Основная функция генерации
function ai_generate_summary(string $content): string
{
  $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
  if (empty($api_key)) {
    return 'Missing OpenAI API key.';
  }

  $prompt = "Please generate a concise summary of the following article in plain text (2–3 sentences):\n\n"
    . mb_substr($content, 0, 3000);

  $response = wp_remote_post(
    'https://api.openai.com/v1/chat/completions',
    [
      'headers' => [
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . $api_key,
      ],
      'body'    => wp_json_encode([
        'model'      => 'gpt-3.5-turbo',
        'messages'   => [
          [
            'role'    => 'system',
            'content' => 'You are an assistant that provides concise article summaries in plain text.'
          ],
          [
            'role'    => 'user',
            'content' => $prompt
          ],
        ],
        'max_tokens' => 150,
      ]),
      'timeout' => 15,
    ]
  );

  if (is_wp_error($response)) {
    return 'Could not connect to the AI service.';
  }
  $code = wp_remote_retrieve_response_code($response);
  if ($code !== 200) {
    return "AI API returned an error (status code: {$code}).";
  }

  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, true);
  $text = trim($data['choices'][0]['message']['content'] ?? '');
  return $text !== '' ? esc_html($text) : 'No summary could be generated.';
}

// Шорткод для вывода summary
function ai_summary_shortcode(): string
{
  if (! is_singular('post')) {
    return '';
  }

  $post_id = get_the_ID();
  $content = get_post_field('post_content', $post_id);

  $cached = get_post_meta($post_id, '_ai_summary', true);
  if ($cached) {
    return "<div class='ai-summary-box'>{$cached}</div>";
  }

  $summary = ai_generate_summary($content);

  // простая фильтрация ошибок
  if (strpos($summary, 'error') === false && strpos($summary, 'Missing') === false && strpos($summary, 'No summary') === false) {
    update_post_meta($post_id, '_ai_summary', $summary);
  }

  return "<div class='ai-summary-box'>{$summary}</div>";
}
add_shortcode('ai_summary', 'ai_summary_shortcode');
