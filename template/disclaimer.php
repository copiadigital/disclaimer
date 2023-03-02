<?php if(get_field('disclaimer_settings_enable_disclaimer', 'option') && !is_page(get_field('disclaimer_settings_deny_page', 'option')) && !is_404()): ?>
  <div class="disclaimer">
    <div class="disclaimer__overlay"></div>

    <div class="disclaimer-modal" id="disclaimerModal">
      <div class="disclaimer-modal-dialog">
        <div class="disclaimer-modal-content">
          <form class="disclaimer__form" name="disclaimer-form" action="" method="post" novalidate>
            <div class="disclaimer-modal-header">
              <?php if(get_field('disclaimer_settings_heading', 'option')): ?>
                <h4 class="disclaimer-modal-title"><?= get_field('disclaimer_settings_heading', 'option'); ?></h4>
              <?php endif; ?>
            </div>

            <div class="disclaimer-modal-body">
              <?php if(get_field('disclaimer_settings_content', 'option')): ?>
                <?php the_field('disclaimer_settings_content', 'option'); ?>
              <?php endif; ?>

              <?php if(get_field('disclaimer_settings_show_country', 'option')): ?>
                <div class="form-group">
                  <select id="countries" name="countries" class="form-control" required aria-label="Country">
                    <option class="default" selected disabled value="">Please select a country</option>
                    <?php if(get_field('disclaimer_settings_countries_all', 'option')): ?>
                      <?php foreach($this->getCountries() as $country): ?>
                        <option value="<?= strtolower($country['code']); ?>"><?= $country['name']; ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <?php foreach(get_field('disclaimer_settings_countries', 'option') as $country): ?>
                        <option value="<?= strtolower($country['value']); ?>"><?= $country['label']; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
              <?php endif; ?>

              <?php if(get_field('disclaimer_settings_show_user_type', 'option')): ?>
                <div class="form-group">
                  <select id="user-types" name="user-types" class="form-control" required aria-label="User type">
                    <option class="default" selected disabled value="">Please select a user type</option>
                    <?php foreach(get_field('disclaimer_settings_user_types', 'option') as $user_type): ?>
                      <?php
                        $selectedCountries = [];
                        foreach($user_type['disclaimer_settings_user_types_selected_countries'] as $selectedCountriesValue){
                          $selectedCountries[] = strtolower($selectedCountriesValue['value']);
                        }
                        $implodedSelectedCountries = implode('|', $selectedCountries);
                        $conditionalSelectedCountries = !empty($implodedSelectedCountries) ? $implodedSelectedCountries : 'all';

                        $user_type['disclaimer_settings_user_types_allow'] = !empty($user_type['disclaimer_settings_user_types_allow']) ? $user_type['disclaimer_settings_user_types_allow'] : 0;
                      ?>
                      <option value="<?= Disclaimer\disclaimerReplaceAll($user_type['disclaimer_settings_user_types_name']); ?>" data-selected-countries="<?= $conditionalSelectedCountries; ?>" data-user-allowed="<?= $user_type['disclaimer_settings_user_types_allow']; ?>">
                        <?= $user_type['disclaimer_settings_user_types_name']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>
              
              <?php if(get_field('disclaimer_settings_country_disclaimers', 'option')): ?>
                <?php foreach(get_field('disclaimer_settings_country_disclaimers', 'option') as $countryDisclaimer): ?>
                  <?php
                    // Loop over country codes and assign them to a new array
                    $countryCodeValues = array();
                  ?>
                  <?php foreach($countryDisclaimer['disclaimer_settings_country_disclaimers_group']['disclaimer_settings_country_disclaimers_group_countries'] as $country): ?>
                    <?php $countryCodeValues[] = $country['value']; ?>
                  <?php endforeach; ?>
                  <?php
                    /*
                    * Get country codes from $countryCodeValues array and convert to string with spaces.
                    * Change to lowercase and assign to variable for use in class attribute.
                    */
                    $countryCodeFormatted = implode(' ', $countryCodeValues);
                    $countryCodeIds = strtolower($countryCodeFormatted);
                  ?>

                  <div class="country-content <?= $countryCodeIds; ?>">
                    <?= $countryDisclaimer['disclaimer_settings_country_disclaimers_group']['disclaimer_settings_country_disclaimers_group_content']; ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

              <?php
              /*
              * Create new array for allowed country codes.
              * Loop over country codes and assign them to a new array.
              */
              $allowedCountryCodes = array();
              ?>
              <?php if(get_field('disclaimer_settings_countries_to_allow', 'option')): ?>
                <?php foreach(get_field('disclaimer_settings_countries_to_allow', 'option') as $allowedCountry): ?>
                  <?php $allowedCountryCodes[] = strtolower($allowedCountry['value']); ?>
                <?php endforeach; ?>
              <?php endif; ?>

              <div class="allowed-countries <?= implode(' ', $allowedCountryCodes); ?>"></div>

              <div class="deny-url" data-deny-page="<?= get_permalink(get_field('disclaimer_settings_deny_page', 'option')); ?>"></div>
            </div>

            <div class="disclaimer-modal-footer">
              <?php if(get_field('disclaimer_settings_show_country', 'option') || get_field('disclaimer_settings_show_user_type', 'option')): ?>
                <div class="form-btns">
                  <button type="submit" class="btn-disclaimer-accept" name="accept" aria-label="Accept disclaimer">Accept</button>
                  <button type="button" class="btn-disclaimer-not-allowed" name="not-allowed" aria-label="Accept disclaimer">Accept</button>
                  <button type="button" class="btn-disclaimer-decline" name="decline" aria-label="Decline disclaimer">Decline</button>
                </div>
              <?php else: ?>
                <div class="form-btns">
                  <button type="submit" class="btn-disclaimer-accept" name="accept" aria-label="Retail accept disclaimer">Retail</button>
                  <button type="button" class="btn-disclaimer-accept" name="not-allowed" aria-label="Retail accept disclaimer">Retail</button>
                  <button type="button" class="btn-disclaimer-decline" name="decline" aria-label="Institutional decline disclaimer">Institutional</button>
                </div>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php endif;
