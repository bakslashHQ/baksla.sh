import googleAnalytics from '@analytics/google-analytics';
import { Controller } from '@hotwired/stimulus';
import Analytics from 'analytics';

/**
 * @property {String} measurementIdValue
 */
export default class extends Controller {
  static values = {
    measurementId: String,
  };

  connect() {
    const analytics = Analytics({
      app: 'baksla.sh',
      plugins: [
        googleAnalytics({
          measurementIds: [this.measurementIdValue],
        }),
      ],
    });

    analytics.page();
  }
}
