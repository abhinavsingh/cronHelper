# cronHelper

Cron jobs are hidden building blocks for most of the websites.
They are generally used to process/aggregate data in the background.
However as a website starts to grow and there is gigabytes of data to be processed by every cron job,
chances are that our cron jobs might overlap and possibly corrupt our data.

cronHelper.php is a utility class to avoid cron job overlap

## Usage

    // edit cronHelper.ini as required
	require 'cronHelper.php';

	if(($pid = cronHelper::lock()) !== FALSE) {
		// write your cron job code here
	
		// unlock once done with the job
		cronHelper::unlock();
	}
