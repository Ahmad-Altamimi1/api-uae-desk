<?php

namespace App\Enums;

enum DocumentType: string
{
    case PASSPORT = 'passport';
    case EMIRATES_ID = 'emirates_id';
    case TRADE_LICENSE = 'trade_license';
    case TAX_CERTIFICATE = 'tax_certificate';
    case CHAMBER_CERTIFICATE = 'chamber_certificate';
    case COMMERCIAL_REGISTER = 'commercial_register';
    case PARTNERSHIP_AGREEMENT = 'partnership_agreement';
    case CORPORATE_TAX_REGISTRATION = 'corporate_tax_registration';
    case VAT_CERTIFICATE = 'vat_certificate';
    case CERTIFICATE_OF_INCORPORATION = 'certificate_of_incorporation';
    case UAE_NATIONAL_ID = 'uae_national_id';
    case POWER_OF_ATTORNEY = 'power_of_attorney';
    case BANK_STATEMENT = 'bank_statement';
    case AUDITED_FINANCIAL_STATEMENT = 'audited_financial_statement';
    case LEASE_AGREEMENT = 'lease_agreement';
    case TRADEMARK_CERTIFICATE = 'trade_mark_certificate';
    case MEMORANDUM_OF_ASSOCIATION = 'memorandum_of_association';
    case SHAREHOLDER_AGREEMENT = 'shareholder_agreement';
    case RECONSIDERATION  = 'Reconsideration ';
    // case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::PASSPORT => __('Passport'),
            self::EMIRATES_ID => __('Emirates ID'),
            self::TRADE_LICENSE => __('Trade License'),
            self::TAX_CERTIFICATE => __('Tax Certificate'),
            self::CHAMBER_CERTIFICATE => __('Chamber Certificate'),
            self::COMMERCIAL_REGISTER => __('Commercial Register'),
            self::PARTNERSHIP_AGREEMENT => __('Partnership Agreement'),
            self::CORPORATE_TAX_REGISTRATION => __('Corporate Tax Registration Certificate'),
            self::VAT_CERTIFICATE => __('VAT Registration Certificate'),
            self::CERTIFICATE_OF_INCORPORATION => __('Certificate of Incorporation'),
            self::UAE_NATIONAL_ID => __('UAE National ID'),
            self::POWER_OF_ATTORNEY => __('Power of Attorney'),
            self::BANK_STATEMENT => __('Bank Statement'),
            self::AUDITED_FINANCIAL_STATEMENT => __('Audited Financial Statement'),
            self::LEASE_AGREEMENT => __('Lease Agreement'),
            self::TRADEMARK_CERTIFICATE => __('Trademark Certificate'),
            self::MEMORANDUM_OF_ASSOCIATION => __('Memorandum of Association'),
            self::SHAREHOLDER_AGREEMENT => __('Shareholder Agreement'),
            // self::OTHER => __('Other'),
            self::RECONSIDERATION => __('Reconsideration '),
        };
    }

    public static function all(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
