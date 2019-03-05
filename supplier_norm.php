<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 05/03/19
 * Time: 15:17
 */

include "config.php";
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';

dol_include_once('/poppy/class/supplier_norm.class.php');


$langs->load("companies");
$langs->load("productbatch");


$id = GETPOST('id', 'int');
$supplierNorm = new TSupplierNorm($db);
$supplierNorm->loadBy($id,'fk_supplier');
$serial_number_start = GETPOST('serial_number_start');
$serial_number_end = GETPOST('serial_number_end');
$lot_number_start = GETPOST('lot_number_start');
$lot_number_end = GETPOST('lot_number_end');
$sellbydate_start = GETPOST('sellbydate_start');
$sellbydate_end = GETPOST('sellbydate_end');
$eatbydate_start = GETPOST('eatbydate_start');
$eatbydate_end = GETPOST('eatbydate_end');
$action = GETPOST('action');

if($action == 'save'){
    $supplierNorm->fk_supplier = $id;
    $supplierNorm->serial_number_start = $serial_number_start;
    $supplierNorm->serial_number_end = $serial_number_end;
    $supplierNorm->lot_number_start = $lot_number_start;
    $supplierNorm->lot_number_end = $lot_number_end;
    $supplierNorm->sellbydate_start = $sellbydate_start;
    $supplierNorm->sellbydate_end = $sellbydate_end;
    $supplierNorm->eatbydate_start = $eatbydate_start;
    $supplierNorm->eatbydate_end = $eatbydate_end;

    $supplierNorm->save();
}

$object = new Societe($db);
$result = $object->fetch($id, $ref);

if(empty($object->fournisseur))accessforbidden();

$title = $langs->trans('SupplierNorm');
$helpurl = "";


if($id > 0 || !empty($ref)) {
    if(!empty($conf->product->enabled)) $upload_dir = $conf->product->multidir_output[$object->entity] . '/' . get_exdir(0, 0, 0, 0, $object, 'product') . dol_sanitizeFileName($object->ref);
    else if(!empty($conf->service->enabled)) $upload_dir = $conf->service->multidir_output[$object->entity] . '/' . get_exdir(0, 0, 0, 0, $object, 'product') . dol_sanitizeFileName($object->ref);

    if(!empty($conf->global->PRODUCT_USE_OLD_PATH_FOR_PHOTO))    // For backward compatiblity, we scan also old dirs
    {
        if(!empty($conf->product->enabled)) $upload_dirold = $conf->product->multidir_output[$object->entity] . '/' . substr(substr("000" . $object->id, -2), 1, 1) . '/' . substr(substr("000" . $object->id, -2), 0, 1) . '/' . $object->id . "/photos";
        else $upload_dirold = $conf->service->multidir_output[$object->entity] . '/' . substr(substr("000" . $object->id, -2), 1, 1) . '/' . substr(substr("000" . $object->id, -2), 0, 1) . '/' . $object->id . "/photos";
    }
}


llxHeader('', $title, $helpurl);
$head = societe_prepare_head($object);
$titre = $langs->trans("ThirdParty");
dol_fiche_head($head, 'suppliernorm', $titre, -1, 'company');

$linkback = '<a href="' . DOL_URL_ROOT . '/societe/list.php?restore_lastsearch_values=1">' . $langs->trans("BackToList") . '</a>';

dol_banner_tab($object, 'tab=thirdparty&id', $linkback, ($user->socid ? 0 : 1), 'rowid', 'nom');

print '<div class="fichecenter">';

// Form to add a new line
print '<form action="' . $_SERVER['PHP_SELF'] . '?tab=' . $tab . '&id=' . $object->id . '" method="POST">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="action" value="save">';

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder" width="100%">';

// Line for title
print '<tr class="liste_titre">';
print '<td align="center">' . $langs->trans('SerialNumber') . ' (' . $langs->trans('Bornes') . ')</td><td align="center">' . $langs->trans('LotNumber') . ' (' . $langs->trans('Bornes') . ')</td>';
print '<td align="center">' . $langs->trans('SellByDate') . ' (' . $langs->trans('Bornes') . ')</td><td align="center">' . $langs->trans('EatByDate') . ' (' . $langs->trans('Bornes') . ')</td><td></td>';
print '</tr>';


print '<!-- line to add new entry --><tr class="oddeven nodrag nodrop nohover">';
print '<td >';
print '<input type="number"  value="'.$supplierNorm->serial_number_start.'" name="serial_number_start">';
print $langs->trans('To');
print '<input type="number"  value="'.$supplierNorm->serial_number_end.'" name="serial_number_end">';
print '</td>';

print '<td >';
print '<input type="number"  value="'.$supplierNorm->lot_number_start.'" name="lot_number_start">';
print $langs->trans('To');
print '<input type="number"  value="'.$supplierNorm->lot_number_end.'" name="lot_number_end">';
print '</td>';

print '<td >';
print '<input type="number"  value="'.$supplierNorm->sellbydate_start.'" name="sellbydate_start">';
print $langs->trans('To');
print '<input type="number"  value="'.$supplierNorm->sellbydate_end.'" name="sellbydate_end">';
print '</td>';

print '<td >';
print '<input type="number"  value="'.$supplierNorm->eatbydate_start.'" name="eatbydate_start">';
print $langs->trans('To');
print '<input type="number"  value="'.$supplierNorm->eatbydate_end.'" name="eatbydate_end">';
print '</td>';
print '<td align="center"><input type="submit" class="button" name="actionadd" value="' . $langs->trans('Save') . '"></td>';
print '</tr>';

print '</table></div></form>';





llxFooter();
$db->close();

