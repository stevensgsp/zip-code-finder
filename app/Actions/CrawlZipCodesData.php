<?php

namespace App\Actions;

use App\Actions\SpoutHelper;
use App\Models\ZipCode;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Goutte\Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Lorisleiva\Actions\Concerns\AsAction;
use ZipArchive;

class CrawlZipCodesData implements ShouldQueue, ShouldBeUnique
{
    use AsAction;

    /**
     * @var int
     */
    public int $jobTries = 5;

    /**
     * @var int
     */
    public int $jobMaxExceptions = 3;

    /**
     * @var int
     */
    public int $jobBackoff = 60 * 5;

    /**
     * @var int
     */
    public int $jobTimeout = 60 * 30;

    /**
     * @var int
     */
    public int $jobUniqueFor = 3600;

    /**
     * @var string
     */
    private string $url = 'https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx';

    /**
     * @return string
     */
    public function getJobUniqueId()
    {
        return time();
    }

    /**
     * @return void
     */
    public function handle()
    {
        // crawl zip file with zip codes
        $filename = $this->crawlZipCodesFile();

        // store Zip Codes from the TXT file
        $this->storeZipCodes($filename);
    }

    /**
     * Crawl Zip Codes file (zip file).
     *
     * @return string
     */
    private function crawlZipCodesFile(): string
    {
        $client = new Client();

        // download ZIP file
        $crawler = $client->request('GET', $this->url);
        $buttonFrom = $crawler->selectButton('btnDescarga');
        $form = $buttonFrom->form();
        $form['rblTipo']->select('txt');
        $client->submit($form);

        // file path
        $filePath = storage_path('app/file.zip');

        // save ZIP file
        $content = $client->getResponse()->getContent();
        $stream = fopen($filePath, 'w');
        fwrite($stream, $content);
        fclose($stream);

        // extract ZIP file
        $filename = $this->extractZipFile($filePath);

        return $filename;
    }

    /**
     * Extract ZIP file.
     *
     * @param  string  $filePath
     * @return string
     */
    private function extractZipFile(string $filePath): string
    {
        $zip = new ZipArchive();

        $status = $zip->open(storage_path('app/file.zip'));

        if ($status !== true) {
            throw new Exception($status);
        }

        $filename = $zip->getNameIndex(0);

        $zip->extractTo(storage_path('app/'));

        $zip->close();

        return $filename;
    }

    /**
     * Store the Zip Codes from the TXT file.
     *
     * @param  string  $filename
     * @return void
     */
    private function storeZipCodes(string $filename): void
    {
        // read the TXT file to store the Zip Codes
        $reader = ReaderEntityFactory::createCSVReader();
        $reader->setFieldDelimiter('|');
        $reader->open(storage_path("app/{$filename}"));

        // truncate the previous Zip Codes in the database
        ZipCode::truncate();

        // map the TXT file to store the Zip Codes
        foreach ($reader->getSheetIterator() as $sheet) {
            // Initialize SpoutHelper specifying the row number which contains the header
            $spoutHelper = new SpoutHelper($sheet, 2);

            foreach ($sheet->getRowIterator() as $key => $row) {
                // skipping headers row
                if (in_array($key, [1, 2])) {
                    continue;
                }

                // Get the indexed array with col name as key and col val as value
                $rowWithHeaderKeys = $spoutHelper->rowWithFormattedHeaders($row->toArray());

                // payload
                $payload = $this->getPayload($rowWithHeaderKeys);

                // get/store zip code
                $zipCode = ZipCode::firstOrCreate(
                    ['zip_code' => $payload['zip_code']],
                    $payload
                );

                // store settlement data
                $zipCode->settlements()->create($payload['settlement']);
            }
        }

        $reader->close();
    }

    /**
     * Returns the payload to store a Zip Code.
     *
     * @param  array  $line
     * @return array
     */
    private function getPayload(array $line): array
    {
        return [
            'zip_code' => utf8_encode($line['d_codigo']),
            'locality' => [
                'key' => (int) utf8_encode($line['c_cve_ciudad']),
                'name' => utf8_encode($line['d_ciudad']),
            ],
            'federal_entity' => [
                'key' => (int) utf8_encode($line['c_estado']),
                'name' => utf8_encode($line['d_estado']),
                'code' => null,
            ],
            'settlement' => [
                'key' => (int) utf8_encode($line['id_asenta_cpcons']),
                'name' => utf8_encode($line['d_asenta']),
                'zone_type' => utf8_encode($line['d_zona']),
                'settlement_type' => [
                    'key' => (int) utf8_encode($line['c_tipo_asenta']),
                    'name' => utf8_encode($line['d_tipo_asenta']),
                ],
            ],
            'municipality' => [
                'key' => (int) utf8_encode($line['c_mnpio']),
                'name' => utf8_encode($line['d_mnpio']),
            ],
        ];
    }
}
