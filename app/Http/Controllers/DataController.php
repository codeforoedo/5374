<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
	public function queryByReferer(Request $request)
	{
		$dataset = json_decode(file_get_contents(base_path('database/files/data.json')), true);

	 	$referer = $request->server('HTTP_REFERER');
		$domain = $request->input('domain') ?: parse_url($referer, PHP_URL_HOST);

		// data.json の aliaes から、データ名を取得する
		$dataName = $this->detectDataName($dataset['domains'], $domain);

		if (!isset($dataset['data'][$dataName])) {
			return abort(403);	// Forbbiden
		}

		// ドメイン固有データを取得する
		$domainData = $dataset['data'][$dataName];

		return response()->json($domainData, 200)
			->header('Access-Control-Allow-Origin', '*')
		;
	}

	protected function detectDataName(array $domains, $domain)
	{
		foreach ($domains as $expression => $dataName) {
			if ($expression == '*') {
				return $dataName;
			}
			elseif ($expression === $domain) {
				return $dataName;
			}
		}

		return null;
	}

	protected function queryByReferer_v1(Request $request)
	{
	 	$referer = $request->server('HTTP_REFERER');
		$refererHost = parse_url($referer, PHP_URL_HOST);
		$domain = $request->input('domain') ?: $refererHost;

		if ($domain === 'shinagawa.5374.codeforoedo.org') {
			$urlPrefix = 'http://data.5374.codeforoedo.org/tokyo/shinagawa/';
		}
		else {
			$urlPrefix = 'http://data.5374.codeforoedo.org/demo/1/';
		}

		return response()->json([
			'settings' => [
			],
			'urls' => [
				'area_days.csv' => $urlPrefix . 'area_days.csv',
				'center.csv' => $urlPrefix . 'center.csv',
				'description.csv' => $urlPrefix . 'description.csv',
				'remarks.csv' => $urlPrefix . 'remarks.csv',
				'target.csv' => $urlPrefix . 'target.csv',
			],
		], 200)
			->header('Access-Control-Allow-Origin', '*')
		;
	}

}
