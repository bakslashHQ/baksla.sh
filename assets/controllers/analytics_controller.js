import googleAnalytics from '@analytics/google-analytics';
import { Controller } from '@hotwired/stimulus';
import Analytics from 'analytics';

/**
 * @property {String} appNameValue
 * @property {String} measurementIdValue
 */
export default class extends Controller {
  static values = {
    appName: String,
    measurementId: String,
  };

  connect() {
    const analytics = Analytics({
      app: this.appNameValue,
      plugins: [
        googleAnalytics({
          measurementIds: [this.measurementIdValue],
          gtagConfig: {
            anonymize_ip: true,
          },
        }),
      ],
    });

    analytics.page();
  }
}
