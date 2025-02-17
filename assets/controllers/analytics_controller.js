import { Controller } from '@hotwired/stimulus';

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
    import('@analytics/google-analytics').then((googleAnalytics) => {
      import('analytics').then((Analytics) => {
        const analytics = Analytics.default({
          app: this.appNameValue,
          plugins: [
            googleAnalytics.default({
              measurementIds: [this.measurementIdValue],
              gtagConfig: {
                anonymize_ip: true,
              },
            }),
          ],
        });

        analytics.page();
      });
    });
  }
}
