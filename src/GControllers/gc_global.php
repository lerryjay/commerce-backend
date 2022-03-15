<?php
class GCGlobal extends GController
{
    public function index()
    {
        $this->countries();
    }

    public function addcountry()
    {
        $this->load->model('localisation');
        $countryId = $this->request->post([
            'name',
            'countrycode',
            'currencyid',
            'weightid',
        ]);
        $insert = $this->addCountry(
            $name,
            $countrycode,
            $currencyId,
            $weightId
        );
        if (!$insert) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error saving country!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Country added successfully',
        ]);
    }

    public function deletecountry()
    {
        $this->load->model('localisation');
        $countryId = $this->request->get('countryid');
        $update = $this->model_localisation->updateCountryStatus($countryId);
        if (!$update) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error deleting country!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Country deleted successfully',
        ]);
    }

    public function editcountry()
    {
        $this->load->model('localisation');
        $update = $this->model_localisation->updateCountry(
            $countryId,
            $name,
            $code,
            $currencyId,
            $weightId
        );
        if (!$update) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error deleting country!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Country updated successfully',
        ]);
    }

    public function countries()
    {
        $this->load->model('localisation');
        $countries = $this->model_localisation->getCountries();
        if (!$countries) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error fetching countries!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Countries retrieved successfully',
            'data' => $countries,
        ]);
    }

    public function addregion()
    {
        $this->load->controller('admin');
        $this->controller_admin->checkRequestPermission('UPDATEREGION');
        $this->load->model('localisation');
        extract($this->request->post(['name', 'countryid', 'state']));
        $insert = $this->model_localisation->addRegion(
            $name,
            $state,
            $countryid
        );
        if (!$insert) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error adding regions!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Region added successfully',
        ]);
    }

    public function deleteregion()
    {
        $this->load->model('localisation');
        $regionid = $this->request->get('regionid');
        $this->load->controller('admin');
        $this->controller_admin->checkRequestPermission('UPDATEREGION');
        $update = $this->model_localisation->updateRegionStatus($regionid);
        if (!$update) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error deleting region!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Region deleted successfully',
        ]);
    }

    public function editregion()
    {
        $this->load->model('localisation');
        $this->load->controller('admin');
        $this->controller_admin->checkRequestPermission('UPDATEREGION');
        extract(
            $this->request->post(['regionid', 'name', 'state', 'countryid'])
        );
        $regions = $this->model_localisation->updateRegion(
            $regionId,
            $name,
            $state,
            $countryId
        );
        if (!$regions) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error updating region!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Update successful',
        ]);
    }

    public function countryregions()
    {
        $this->load->model('localisation');
        $countryId = $this->request->get('countryid');
        $regions = $this->model_localisation->getCountryRegions($countryId);
        if (!$regions) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error fetching regions!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Countries retrieved successfully',
            'data' => $regions,
        ]);
    }

    public function countrystates()
    {
        $this->load->model('localisation');
        $countryId = $this->request->get('countryid');
        $regions = $this->model_localisation->getCountryStates($countryId);
        if (!$regions) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error fetching regions!',
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Countries retrieved successfully',
            'data' => $regions,
        ]);
    }

    public function manufacturers()
    {
        $this->load->model('brands');
        $manufacturers = $this->model_brands->listMarketManufacturers();
        if (!$manufacturers) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error fetching manufacturers!',
            ]);
        }
        $this->request->emit($manufacturers);
    }

    public function brands()
    {
        $this->load->model('brands');
        $manufacturerId = $this->request->get('manufacturerid');
        $brands = $this->model_brands->getManufacturerBrands($manufacturerId);
        if (!$brands) {
            $this->request->emit([
                'error' => true,
                'message' => 'Error fetching brands!',
            ]);
        }
        $this->request->emit($brands);
    }
}
?>
