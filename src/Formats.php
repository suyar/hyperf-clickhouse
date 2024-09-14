<?php

declare(strict_types=1);
/**
 * This file is part of suyar/hyperf-clickhouse.
 *
 * @link     https://github.com/suyar/hyperf-clickhouse
 * @document https://github.com/suyar/hyperf-clickhouse/blob/main/README.md
 * @contact  su@zorzz.com
 * @license  https://github.com/suyar/hyperf-clickhouse/blob/master/LICENSE
 */

namespace Suyar\ClickHouse;

/**
 * @see https://clickhouse.com/docs/en/interfaces/formats
 */
class Formats
{
    public const TabSeparated = 'TabSeparated';

    public const TabSeparatedRaw = 'TabSeparatedRaw';

    public const TabSeparatedWithNames = 'TabSeparatedWithNames';

    public const TabSeparatedWithNamesAndTypes = 'TabSeparatedWithNamesAndTypes';

    public const TabSeparatedRawWithNames = 'TabSeparatedRawWithNames';

    public const TabSeparatedRawWithNamesAndTypes = 'TabSeparatedRawWithNamesAndTypes';

    public const Template = 'Template';

    public const TemplateIgnoreSpaces = 'TemplateIgnoreSpaces';

    public const CSV = 'CSV';

    public const CSVWithNames = 'CSVWithNames';

    public const CSVWithNamesAndTypes = 'CSVWithNamesAndTypes';

    public const CustomSeparated = 'CustomSeparated';

    public const CustomSeparatedWithNames = 'CustomSeparatedWithNames';

    public const CustomSeparatedWithNamesAndTypes = 'CustomSeparatedWithNamesAndTypes';

    public const SQLInsert = 'SQLInsert';

    public const Values = 'Values';

    public const Vertical = 'Vertical';

    public const JSON = 'JSON';

    public const JSONAsString = 'JSONAsString';

    public const JSONAsObject = 'JSONAsObject';

    public const JSONStrings = 'JSONStrings';

    public const JSONColumns = 'JSONColumns';

    public const JSONColumnsWithMetadata = 'JSONColumnsWithMetadata';

    public const JSONCompact = 'JSONCompact';

    public const JSONCompactStrings = 'JSONCompactStrings';

    public const JSONCompactColumns = 'JSONCompactColumns';

    public const JSONCompactWithProgress = 'JSONCompactWithProgress';

    public const JSONEachRow = 'JSONEachRow';

    public const PrettyJSONEachRow = 'PrettyJSONEachRow';

    public const JSONEachRowWithProgress = 'JSONEachRowWithProgress';

    public const JSONStringsEachRow = 'JSONStringsEachRow';

    public const JSONStringsEachRowWithProgress = 'JSONStringsEachRowWithProgress';

    public const JSONCompactEachRow = 'JSONCompactEachRow';

    public const JSONCompactEachRowWithNames = 'JSONCompactEachRowWithNames';

    public const JSONCompactEachRowWithNamesAndTypes = 'JSONCompactEachRowWithNamesAndTypes';

    public const JSONCompactStringsEachRow = 'JSONCompactStringsEachRow';

    public const JSONCompactStringsEachRowWithNames = 'JSONCompactStringsEachRowWithNames';

    public const JSONCompactStringsEachRowWithNamesAndTypes = 'JSONCompactStringsEachRowWithNamesAndTypes';

    public const JSONObjectEachRow = 'JSONObjectEachRow';

    public const BSONEachRow = 'BSONEachRow';

    public const TSKV = 'TSKV';

    public const Pretty = 'Pretty';

    public const PrettyNoEscapes = 'PrettyNoEscapes';

    public const PrettyMonoBlock = 'PrettyMonoBlock';

    public const PrettyNoEscapesMonoBlock = 'PrettyNoEscapesMonoBlock';

    public const PrettyCompact = 'PrettyCompact';

    public const PrettyCompactNoEscapes = 'PrettyCompactNoEscapes';

    public const PrettyCompactMonoBlock = 'PrettyCompactMonoBlock';

    public const PrettyCompactNoEscapesMonoBlock = 'PrettyCompactNoEscapesMonoBlock';

    public const PrettySpace = 'PrettySpace';

    public const PrettySpaceNoEscapes = 'PrettySpaceNoEscapes';

    public const PrettySpaceMonoBlock = 'PrettySpaceMonoBlock';

    public const PrettySpaceNoEscapesMonoBlock = 'PrettySpaceNoEscapesMonoBlock';

    public const Prometheus = 'Prometheus';

    public const Protobuf = 'Protobuf';

    public const ProtobufSingle = 'ProtobufSingle';

    public const ProtobufList = 'ProtobufList';

    public const Avro = 'Avro';

    public const AvroConfluent = 'AvroConfluent';

    public const Parquet = 'Parquet';

    public const ParquetMetadata = 'ParquetMetadata';

    public const Arrow = 'Arrow';

    public const ArrowStream = 'ArrowStream';

    public const ORC = 'ORC';

    public const One = 'One';

    public const Npy = 'Npy';

    public const RowBinary = 'RowBinary';

    public const RowBinaryWithNames = 'RowBinaryWithNames';

    public const RowBinaryWithNamesAndTypes = 'RowBinaryWithNamesAndTypes';

    public const RowBinaryWithDefaults = 'RowBinaryWithDefaults';

    public const Native = 'Native';

    public const Null = 'Null';

    public const XML = 'XML';

    public const CapnProto = 'CapnProto';

    public const LineAsString = 'LineAsString';

    public const Regexp = 'Regexp';

    public const RawBLOB = 'RawBLOB';

    public const MsgPack = 'MsgPack';

    public const MySQLDump = 'MySQLDump';

    public const DWARF = 'DWARF';

    public const Form = 'Form';
}
