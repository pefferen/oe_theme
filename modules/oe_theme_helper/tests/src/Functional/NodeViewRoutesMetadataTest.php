<?php

declare(strict_types=1);

namespace Drupal\Tests\oe_theme_helper\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\content_moderation\Traits\ContentModerationTestTrait;
use Drupal\filter\Entity\FilterFormat;

/**
 * Tests the base metadata class for node view routes.
 *
 * @group batch3
 */
class NodeViewRoutesMetadataTest extends BrowserTestBase {

  use ContentModerationTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'content_moderation',
    'node',
    'oe_theme_helper',
    'page_header_metadata_test',
    'workflows',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with permission to see revisions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Enable oe_theme and set it as default.
    $this->assertTrue($this->container->get('theme_installer')->install(['oe_theme']));
    $this->container->get('config.factory')
      ->getEditable('system.theme')
      ->set('default', 'oe_theme')
      ->save();

    // Rebuild the ui_pattern definitions to collect the ones provided by
    // oe_theme itself.
    $this->container->get('plugin.manager.ui_patterns')->clearCachedDefinitions();

    $this->drupalCreateContentType(['type' => 'test', 'name' => 'Moderated'])->save();

    $workflow = $this->createEditorialWorkflow();
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'test');
    $workflow->save();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'view latest version',
      'view any unpublished content',
    ]);

    FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ])->save();
  }

  /**
   * Tests that the correct metadata is retrieved on node view routes.
   */
  public function testNodeRoutes(): void {
    // Create a published revision for a node.
    $published_revision_body = '<u>Custom page</u> introduction with <sub>rich</sub><sup>text</sup><mark>highlighted</mark>.';
    $node = $this->drupalCreateNode([
      'type' => 'test',
      'moderation_state' => 'published',
      'body' => [
        'value' => $published_revision_body,
        'format' => 'full_html',
      ],
    ]);
    // Save the revision url for later access.
    $first_revision_url = $node->toUrl('revision');

    // Create a forward, non-published revision of it.
    $draft_revision_body = $this->randomString();
    $node->set('body', $draft_revision_body);
    $node->set('moderation_state', 'draft');
    $node->save();

    $this->drupalLogin($this->adminUser);

    // Verify that the page header block is shown in the node canonical route
    // and contains the correct revision text.
    $this->drupalGet($node->toUrl());
    $this->assertEquals($published_revision_body, $this->getSession()->getPage()->find('css', '.ecl-page-header__description')->getHtml());

    // Verify that the block is also shown in the latest version route with the
    // correct draft revision loaded.
    $this->drupalGet($node->toUrl('latest-version'));
    $this->assertSession()->elementTextContains('css', '.ecl-page-header__description', $draft_revision_body);

    // Verify also for the node single revision route.
    $this->drupalGet($first_revision_url);
    $this->assertEquals($published_revision_body, $this->getSession()->getPage()->find('css', '.ecl-page-header__description')->getHtml());
  }

}
